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
    private function isValidSubscription($subscription)
    {
        if (!$subscription) {
            return false;
        }

        $validStatuses = ['active', 'trialing', 'paid'];
        if (!isset($subscription->stripe_status) || !in_array($subscription->stripe_status, $validStatuses)) {
            return false;
        }

        $expiry = $subscription->last_recurrent_date ?? $subscription->availed_date ?? null;
        if ($expiry) {
            try {
                $expiryDate = \Carbon\Carbon::parse($expiry);
                if ($expiryDate->isPast()) {
                    return false;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return true;
    }

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

            $current_subscription = SubscriptionPlan::where('availed_by_uid', $patient_id)
                ->whereIn('stripe_status', ['active', 'trialing', 'paid'])
                ->first();

            $is_valid_subscription = $this->isValidSubscription($current_subscription);

            $this_month_appointments = Appointment::where('patient_id', $patient_id)
                ->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])
                ->count();

            $prefixcodes = Settings::where('id', 1)->value('prefixcode');

            return response()->json([
                'type' => 'success',
                'data' => [
                    'patient_id' => $patient_id,
                    'fname' => $details->fname ?? null,
                    'lname' => $details->lname ?? null,
                    'email' => $details->email ?? null,
                    'has_active_subscription' => $is_valid_subscription,
                    'this_month_appointments' => $this_month_appointments,
                    'prefix_codes' => $prefixcodes
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

            $validator = Validator::make($request->all(), [
                'date' => 'required|date',
                'time' => 'required',
                'plan' => 'required|in:subscription,medicare'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $patient_id = $user->patient_id;

            $check_if_exists = Appointment::where('booked_by', $user->id)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->count();

            if ($check_if_exists > 0) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'You already booked an appointment in the same date: ' . $request->date . ' and time: ' . $request->time
                ], 400);
            }

            $plan = $request->plan;

            if ($plan == 'subscription') {
                $current_subscription = SubscriptionPlan::where('availed_by_uid', $patient_id)
                    ->whereIn('stripe_status', ['active', 'trialing', 'paid'])
                    ->first();

                $is_valid_subscription = $this->isValidSubscription($current_subscription);

                if (!$current_subscription || !$is_valid_subscription) {
                    $message = 'You need to have an active subscription to book an appointment.';

                    if ($current_subscription && !$is_valid_subscription) {
                        $expiryDate = \Carbon\Carbon::parse(
                            $current_subscription->last_recurrent_date ?? $current_subscription->availed_date
                        )->format('F j, Y');

                        $message = "Your subscription expired on {$expiryDate}. Please renew your subscription to book appointments.";
                    }

                    return response()->json([
                        'type' => 'error',
                        'message' => $message
                    ], 400);
                }

                return response()->json([
                    'type' => 'success',
                    'data' => [
                        'requires_payment' => false,
                        'booking_details' => [
                            'date' => $request->date,
                            'time' => $request->time,
                            'plan' => $plan
                        ]
                    ]
                ], 200);
            }

            $settings = Settings::first();
            if (!$settings) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'System configuration not found'
                ], 500);
            }

            $amount = $settings->medicare_amount;
            $currency = $settings->currency;
            $stripe_client_id = $settings->stripe_client_id;

            return response()->json([
                'type' => 'success',
                'data' => [
                    'requires_payment' => true,
                    'stripe_key' => $stripe_client_id,
                    'amount' => $amount,
                    'currency' => $currency,
                    'booking_details' => [
                        'date' => $request->date,
                        'time' => $request->time,
                        'plan' => $plan
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

            // Base validation rules
            $validationRules = [
                'date' => 'required|date',
                'time' => 'required',
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'required|email',
                'plan' => 'required|in:subscription,medicare',
                'insurance_company' => 'required|string',
                'policy_id' => 'required|string',
                'insurance_plan_type' => 'required|string',
                'chief_complaint' => 'required|string',
                'symptom_onset' => 'required|string',
                'current_medications' => 'required|string',
                'allergies' => 'required|string',
                'past_surgical_history' => 'required|string'
            ];

            // Add payment validation for medicare plan
            if ($request->plan == 'medicare') {
                $validationRules['payment_intent_id'] = 'required|string';
                $validationRules['charge_id'] = 'required|string';
                $validationRules['payment_status'] = 'required|string';
                $validationRules['amount'] = 'required|numeric';
                $validationRules['currency'] = 'required|string';
                $validationRules['users_full_name'] = 'required|string';
                $validationRules['users_address'] = 'required|string';
                $validationRules['users_email'] = 'required|email';
                $validationRules['users_phone'] = 'required|string';
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
            $exists = Appointment::where('booked_by', $userID)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->count();

            if ($exists > 0) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'You already booked an appointment at this date and time'
                ], 400);
            }

            // Upload insurance cards if provided
            $frontInsurancePath = $request->insurance_card_front
                ? $this->uploadBase64Image($request->insurance_card_front, 'frontInsurance')
                : null;

            $backInsurancePath = $request->insurance_card_back
                ? $this->uploadBase64Image($request->insurance_card_back, 'backInsurance')
                : null;

            // Generate appointment UID
            $prefix = 'SA';
            $year = date('y');
            $month = date('m');
            $count = Appointment::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count() + 1;
            $appointment_uid = $prefix . $year . $month . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $plan = $request->plan;

            // Handle subscription plan
            if ($plan == 'subscription') {
                $subscription = SubscriptionPlan::where('availed_by_uid', $patient_id)
                    ->whereIn('stripe_status', ['active', 'trialing', 'paid'])
                    ->first();

                if (!$this->isValidSubscription($subscription)) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Your subscription is inactive or expired.'
                    ], 400);
                }

                Appointment::create([
                    'appointment_uid' => $appointment_uid,
                    'fname' => $request->fname,
                    'lname' => $request->lname,
                    'email' => $request->email,
                    'patient_id' => $patient_id,
                    'date' => $request->date,
                    'time' => $request->time,
                    'booked_by' => $userID,
                    'users_full_name' => $request->fname . ' ' . $request->lname,
                    'insurance_company' => $request->insurance_company,
                    'policyholder_name' => $request->policyholder_name,
                    'policy_id' => $request->policy_id,
                    'group_number' => $request->group_number,
                    'insurance_plan_type' => $request->insurance_plan_type,
                    'chief_complaint' => $request->chief_complaint,
                    'symptom_onset' => $request->symptom_onset,
                    'prior_diagnoses' => $request->prior_diagnoses,
                    'current_medications' => $request->current_medications,
                    'allergies' => $request->allergies,
                    'past_surgical_history' => $request->past_surgical_history,
                    'family_medical_history' => $request->family_medical_history,
                    'plan' => 'subscription',
                    'insurance_card_front' => $frontInsurancePath,
                    'insurance_card_back' => $backInsurancePath,
                ]);

                Notification::create([
                    'user_id' => $patient_id,
                    'user_type' => 'patient',
                    'notification' => 'Using Your Subscription Plan, You\'ve booked an appointment at ' . date('g:i A', strtotime($request->time)) . ' on ' . date('j F, Y', strtotime($request->date)),
                ]);

                return response()->json([
                    'type' => 'success',
                    'message' => 'Booking Successful!',
                    'data' => [
                        'appointment_uid' => $appointment_uid,
                        'date' => $request->date,
                        'time' => $request->time,
                        'plan' => 'subscription'
                    ]
                ], 201);
            }

            // Handle medicare plan - payment already completed on frontend
            $appointment = Appointment::create([
                'appointment_uid' => $appointment_uid,
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'patient_id' => $patient_id,
                'date' => $request->date,
                'time' => $request->time,
                'booked_by' => $userID,
                'users_full_name' => $request->users_full_name,
                'users_address' => $request->users_address,
                'users_email' => $request->users_email,
                'users_phone' => $request->users_phone,
                'country_code' => $request->country_code,
                'stripe_charge_id' => $request->charge_id,
                'stripe_payment_intent_id' => $request->payment_intent_id,
                'payment_status' => $request->payment_status,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'insurance_company' => $request->insurance_company,
                'policyholder_name' => $request->policyholder_name,
                'policy_id' => $request->policy_id,
                'group_number' => $request->group_number,
                'insurance_plan_type' => $request->insurance_plan_type,
                'chief_complaint' => $request->chief_complaint,
                'symptom_onset' => $request->symptom_onset,
                'prior_diagnoses' => $request->prior_diagnoses,
                'current_medications' => $request->current_medications,
                'allergies' => $request->allergies,
                'past_surgical_history' => $request->past_surgical_history,
                'family_medical_history' => $request->family_medical_history,
                'plan' => 'medicare',
                'insurance_card_front' => $frontInsurancePath,
                'insurance_card_back' => $backInsurancePath,
            ]);

            Notification::create([
                'user_id' => $patient_id,
                'user_type' => 'patient',
                'notification' => 'Using Medicare Payment, You\'ve booked an appointment at ' . date('g:i A', strtotime($request->time)) . ' on ' . date('j F, Y', strtotime($request->date)),
            ]);

            return response()->json([
                'type' => 'success',
                'message' => 'Payment and booking completed successfully!',
                'data' => [
                    'appointment_id' => $appointment->id,
                    'appointment_uid' => $appointment_uid,
                    'date' => $request->date,
                    'time' => $request->time,
                    'plan' => 'medicare',
                    'payment_details' => [
                        'payment_intent_id' => $request->payment_intent_id,
                        'charge_id' => $request->charge_id,
                        'payment_status' => $request->payment_status,
                        'amount' => $request->amount,
                        'currency' => $request->currency
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Booking failed', [
                'user_id' => $user->id ?? 'unknown',
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
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                $extension = $matches[1];
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            } else {
                $extension = 'jpg';
            }

            $imageData = base64_decode($base64String);

            if ($imageData === false) {
                Log::warning('Failed to decode base64 image', ['type' => $type]);
                return null;
            }

            if (strlen($imageData) > 5 * 1024 * 1024) {
                Log::warning('Image too large', ['type' => $type, 'size' => strlen($imageData)]);
                return null;
            }

            $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
            file_put_contents($tempFile, $imageData);

            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                'insurance_' . $type . '.' . $extension,
                mime_content_type($tempFile),
                null,
                true
            );

            $result = $this->uploadFile($uploadedFile, $type);

            @unlink($tempFile);

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to upload base64 image', [
                'type' => $type,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    private function uploadFile($file, $type)
    {
        if ($file) {
            $extension = $file->getClientOriginalExtension();
            $filename = 'insurance_' . $type . '_' . rand(1111111111, 9999999999) . '.' . $extension;
            $path = $type . '/';
            $file->move(public_path($path), $filename);
            return $path . $filename;
        }
        return null;
    }
}