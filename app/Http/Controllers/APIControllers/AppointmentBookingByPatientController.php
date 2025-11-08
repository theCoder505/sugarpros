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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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
            Log::error('Failed to fetch patient details', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to fetch patient details'
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

            $validator = Validator::make($request->all(), [
                'date' => 'required|date|after:today',
                'time' => 'required',
                'plan' => 'required|in:subscription,medicare,cash'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

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
            if (!$settings) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'System configuration not found'
                ], 500);
            }

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
            Log::error('Failed to initiate booking', [
                'user_id' => $user->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'type' => 'error',
                'message' => 'Failed to initiate booking'
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
                'date' => 'required|date|after:today',
                'time' => 'required',
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'required|email',
                'plan' => 'required|in:subscription,medicare,cash'
            ];

            // Add payment validation if not subscription
            if ($request->plan != 'subscription') {
                $validationRules['stripe_token'] = 'required|string';
                $validationRules['users_full_name'] = 'required|string|max:255';
                $validationRules['users_address'] = 'required|string|max:500';
                $validationRules['users_email'] = 'required|email';
                $validationRules['users_phone'] = 'required|string|max:20';
                $validationRules['country_code'] = 'required|string|max:10';
            }

            $validator = Validator::make($request->all(), $validationRules);

            if ($validator->fails()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for duplicate appointment
            $duplicateExists = Appointment::where('booked_by', $userID)
                ->where('patient_id', $patient_id)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->exists();

            if ($duplicateExists) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'You already booked an appointment for this date and time'
                ], 400);
            }

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
            if (!$settings) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'System configuration not found'
                ], 500);
            }

            $charge = null;
            $amount = 0;
            $notification = '';

            // Handle payment based on plan
            if ($request->plan == 'subscription') {
                // Verify subscription exists in database
                $current_subscription = SubscriptionPlan::where('availed_by_uid', $patient_id)
                    ->whereIn('stripe_status', ['active', 'trialing'])
                    ->first();

                if (!$current_subscription) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'No active subscription found. Please subscribe first.'
                    ], 400);
                }

                // Verify subscription exists in Stripe
                try {
                    Stripe\Stripe::setApiKey($settings->stripe_secret_key);
                    $stripe_subscription = Stripe\Subscription::retrieve($current_subscription->stripe_charge_id);

                    // Subscription exists in Stripe, proceed normally
                    $notification = 'Using Your Subscription Plan, You\'ve booked an appointment at ' .
                        date('g:i A', strtotime($request->time)) . ' on ' .
                        date('j F, Y', strtotime($request->date));
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Stripe subscription not found, but local record exists
                    // Allow booking to proceed (subscription might have been deleted in Stripe but is still valid locally)
                    $notification = 'Using Your Subscription Plan, You\'ve booked an appointment at ' .
                        date('g:i A', strtotime($request->time)) . ' on ' .
                        date('j F, Y', strtotime($request->date));

                    Log::warning('Subscription exists locally but not in Stripe', [
                        'patient_id' => $patient_id,
                        'subscription_id' => $current_subscription->id,
                        'stripe_charge_id' => $current_subscription->stripe_charge_id
                    ]);
                }
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

                try {
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
                            'appointment_uid' => $appointmentUid,
                        ]
                    ]);

                    // Verify charge was successful
                    if (!$charge->paid) {
                        return response()->json([
                            'type' => 'error',
                            'message' => 'Payment failed: Charge not completed'
                        ], 400);
                    }

                } catch (\Stripe\Exception\CardException $e) {
                    Log::error('Stripe card payment failed', [
                        'patient_id' => $patient_id,
                        'error' => $e->getError()->message
                    ]);
                    
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Payment failed: ' . $e->getError()->message
                    ], 400);
                    
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    Log::error('Stripe invalid request', [
                        'patient_id' => $patient_id,
                        'error' => $e->getMessage()
                    ]);

                    // Handle invalid token specifically
                    if (str_contains($e->getMessage(), 'No such token')) {
                        return response()->json([
                            'type' => 'error',
                            'message' => 'Invalid payment token. Please refresh the page and try again.'
                        ], 400);
                    }

                    return response()->json([
                        'type' => 'error',
                        'message' => 'Payment processing error: ' . $e->getMessage()
                    ], 400);
                    
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    Log::error('Stripe authentication failed', [
                        'patient_id' => $patient_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Payment system configuration error'
                    ], 500);
                    
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    Log::error('Stripe API connection failed', [
                        'patient_id' => $patient_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Network error. Please try again.'
                    ], 503);
                    
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    Log::error('Stripe API error', [
                        'patient_id' => $patient_id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Payment system error: ' . $e->getMessage()
                    ], 500);
                }
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

            Log::info('Appointment booked successfully', [
                'appointment_uid' => $appointmentUid,
                'patient_id' => $patient_id,
                'plan' => $request->plan
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Appointment booked successfully!',
                'data' => $responseData
            ], 201);

        } catch (\Exception $e) {
            Log::error('Booking failed unexpectedly', [
                'patient_id' => $patient_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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
                Log::warning('Failed to decode base64 image', ['type' => $type]);
                return null;
            }

            // Validate image size (max 5MB)
            if (strlen($imageData) > 5 * 1024 * 1024) {
                Log::warning('Image too large', ['type' => $type, 'size' => strlen($imageData)]);
                return null;
            }

            $filename = 'insurance_' . $type . '_' . time() . '_' . rand(1000, 9999) . '.' . $extension;
            $path = 'uploads/insurance/' . $type . '/';

            // Create directory if it doesn't exist
            if (!file_exists(public_path($path))) {
                mkdir(public_path($path), 0755, true);
            }

            $fullPath = public_path($path . $filename);
            
            if (file_put_contents($fullPath, $imageData) === false) {
                Log::error('Failed to save image file', ['path' => $fullPath]);
                return null;
            }

            return $path . $filename;
        } catch (\Exception $e) {
            Log::error('Failed to upload base64 image', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
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