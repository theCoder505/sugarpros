<?php

namespace App\Http\Controllers\APIControllers;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Notification;
use App\Models\Settings;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Stripe;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppointmentBookingByPatientController extends Controller
{
    public function getPatientDetails()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;

            $details = UserDetails::where('user_id', $userID)
                ->first(['fname', 'lname', 'email']);

            // Check for active subscription
            $current_subscription = SubscriptionPlan::where('availed_by_uid', $patient_id)
                ->whereIn('stripe_status', ['active', 'trialing'])
                ->first();

            // Count this month's appointments
            $this_month_appointments = Appointment::where('patient_id', $patient_id)
                ->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])
                ->count();

            return response()->json([
                'type' => 'success',
                'data' => [
                    'patient_id' => $patient_id,
                    'fname' => $details->fname ?? null,
                    'lname' => $details->lname ?? null,
                    'email' => $details->email ?? null,
                    'has_active_subscription' => $current_subscription ? true : false,
                    'subscription_plan' => $current_subscription ? $current_subscription->plan_name : null,
                    'this_month_appointments' => $this_month_appointments,
                    'prefix_codes' => Settings::where('id', 1)->value('prefixcode')
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch patient details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function initiateBooking(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;

            $request->validate([
                'date' => 'required|date',
                'time' => 'required',
                'plan' => 'required|in:subscription,medicare,cash'
            ]);

            // Check for existing appointment
            $exists = Appointment::where('booked_by', $userID)
                ->where('patient_id', $patient_id)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->exists();

            if ($exists) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'You already booked an appointment for this date and time'
                ], 400);
            }

            // Check subscription if plan is subscription
            if ($request->plan == 'subscription') {
                $current_subscription = SubscriptionPlan::where('availed_by_uid', $patient_id)
                    ->whereIn('stripe_status', ['active', 'trialing'])
                    ->first();

                if (!$current_subscription) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'You need to have an active subscription to book an appointment.'
                    ], 400);
                }
            }

            $settings = Settings::first();
            
            // Determine amount based on plan
            $amount = 0;
            $requiresPayment = true;
            
            if ($request->plan == 'subscription') {
                $requiresPayment = false;
            } elseif ($request->plan == 'medicare') {
                $amount = $settings->medicare_amount;
            } elseif ($request->plan == 'cash') {
                $amount = $settings->stripe_amount;
            }

            return response()->json([
                'type' => 'success',
                'data' => [
                    'requires_payment' => $requiresPayment,
                    'stripe_key' => $requiresPayment ? $settings->stripe_client_id : null,
                    'amount' => $amount,
                    'currency' => $settings->currency,
                    'booking_details' => [
                        'date' => $request->date,
                        'time' => $request->time,
                        'plan' => $request->plan
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to initiate booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function completeBooking(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            $userID = $user->id;
            $patient_id = $user->patient_id;

            // Base validation
            $validationRules = [
                'date' => 'required|date',
                'time' => 'required',
                'fname' => 'required',
                'lname' => 'required',
                'email' => 'required|email',
                'plan' => 'required|in:subscription,medicare,cash'
            ];

            // Add payment validation if not subscription
            if ($request->plan != 'subscription') {
                $validationRules['stripe_token'] = 'required';
                $validationRules['users_full_name'] = 'required';
                $validationRules['users_address'] = 'required';
                $validationRules['users_email'] = 'required|email';
                $validationRules['users_phone'] = 'required';
                $validationRules['country_code'] = 'required';
            }

            $request->validate($validationRules);

            // Generate appointment UID
            $prefix = 'SA';
            $year = date('y');
            $month = date('m');
            $currentMonthCount = Appointment::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count();
            $sequence = str_pad($currentMonthCount + 1, 4, '0', STR_PAD_LEFT);
            $appointmentUid = $prefix . $year . $month . '-' . $sequence;

            $settings = Settings::first();
            $charge = null;
            $amount = 0;
            $notification = '';

            // Handle payment based on plan
            if ($request->plan == 'subscription') {
                // No payment needed for subscription
                $notification = 'Using Your Subscription Plan, You\'ve booked an appointment at ' . 
                    date('g:i A', strtotime($request->time)) . ' on ' . 
                    date('j F, Y', strtotime($request->date));
            } else {
                // Process payment for medicare or cash
                if ($request->plan == 'medicare') {
                    $amount = $settings->medicare_amount;
                    $notification = 'Using Medicare Payment, You\'ve booked an appointment at ' . 
                        date('g:i A', strtotime($request->time)) . ' on ' . 
                        date('j F, Y', strtotime($request->date));
                } elseif ($request->plan == 'cash') {
                    $amount = $settings->stripe_amount;
                    $notification = 'Using Direct Cash Payment, You\'ve booked an appointment at ' . 
                        date('g:i A', strtotime($request->time)) . ' on ' . 
                        date('j F, Y', strtotime($request->date));
                }

                Stripe\Stripe::setApiKey($settings->stripe_secret_key);

                $charge = Stripe\Charge::create([
                    'amount' => $amount * 100,
                    'currency' => strtolower($settings->currency),
                    'source' => $request->stripe_token,
                    'description' => 'Appointment booking for ' . $request->users_full_name,
                    'receipt_email' => $request->users_email,
                    'metadata' => [
                        'patient_id' => $patient_id,
                        'appointment_date' => $request->date,
                        'appointment_time' => $request->time,
                    ]
                ]);
            }

            // Handle insurance card uploads if provided (base64 encoded)
            $frontInsurancePath = null;
            $backInsurancePath = null;

            if ($request->has('insurance_card_front') && $request->insurance_card_front) {
                $frontInsurancePath = $this->uploadBase64Image($request->insurance_card_front, 'frontInsurance');
            }

            if ($request->has('insurance_card_back') && $request->insurance_card_back) {
                $backInsurancePath = $this->uploadBase64Image($request->insurance_card_back, 'backInsurance');
            }

            // Create appointment data
            $appointmentData = [
                'appointment_uid' => $appointmentUid,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'patient_id' => $patient_id,
                'date' => $request->date,
                'time' => $request->time,
                'booked_by' => $userID,
                'plan' => $request->plan,
                'insurance_company' => $request->insurance_company ?? null,
                'policyholder_name' => $request->policyholder_name ?? null,
                'policy_id' => $request->policy_id ?? null,
                'group_number' => $request->group_number ?? null,
                'insurance_plan_type' => $request->insurance_plan_type ?? null,
                'chief_complaint' => $request->chief_complaint ?? null,
                'symptom_onset' => $request->symptom_onset ?? null,
                'prior_diagnoses' => $request->prior_diagnoses ?? null,
                'current_medications' => $request->current_medications ?? null,
                'allergies' => $request->allergies ?? null,
                'past_surgical_history' => $request->past_surgical_history ?? null,
                'family_medical_history' => $request->family_medical_history ?? null,
                'insurance_card_front' => $frontInsurancePath,
                'insurance_card_back' => $backInsurancePath,
            ];

            // Add payment details if payment was processed
            if ($charge) {
                $appointmentData['users_full_name'] = $request->users_full_name;
                $appointmentData['users_address'] = $request->users_address;
                $appointmentData['users_email'] = $request->users_email;
                $appointmentData['users_phone'] = $request->users_phone;
                $appointmentData['country_code'] = $request->country_code;
                $appointmentData['stripe_charge_id'] = $charge->id;
                $appointmentData['payment_status'] = 'completed';
                $appointmentData['amount'] = $amount;
                $appointmentData['currency'] = $settings->currency;
            } else {
                // For subscription, set full name from fname and lname
                $appointmentData['users_full_name'] = $request->fname . ' ' . $request->lname;
            }

            // Create appointment
            $appointment = Appointment::create($appointmentData);

            // Create notification
            Notification::create([
                'user_id' => $patient_id,
                'user_type' => 'patient',
                'notification' => $notification,
            ]);

            $responseData = [
                'appointment_id' => $appointment->id,
                'appointment_uid' => $appointmentUid,
                'date' => $appointment->date,
                'time' => $appointment->time,
                'plan' => $appointment->plan
            ];

            if ($charge) {
                $responseData['payment_details'] = [
                    'amount' => $amount,
                    'currency' => $settings->currency,
                    'charge_id' => $charge->id
                ];
            }

            return response()->json([
                'type' => 'success',
                'message' => 'Appointment booked successfully!',
                'data' => $responseData
            ], 201);

        } catch (\Stripe\Exception\CardException $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Payment failed: ' . $e->getError()->message
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'type' => 'error',
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function uploadBase64Image($base64String, $type)
    {
        try {
            // Check if the string contains the data URL prefix
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                $extension = $matches[1];
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            } else {
                $extension = 'jpg'; // Default extension
            }

            $imageData = base64_decode($base64String);
            
            if ($imageData === false) {
                return null;
            }

            $filename = 'insurance_' . $type . '_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = $type . '/';
            
            // Create directory if it doesn't exist
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0755, true);
            }

            file_put_contents(public_path($path . $filename), $imageData);
            
            return $path . $filename;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function paymentSuccess(Request $request)
    {
        return response()->json([
            'type' => 'success',
            'message' => 'Payment completed successfully'
        ], 200);
    }

    public function paymentCancel()
    {
        return response()->json([
            'type' => 'error',
            'message' => 'Payment was cancelled'
        ], 400);
    }
}