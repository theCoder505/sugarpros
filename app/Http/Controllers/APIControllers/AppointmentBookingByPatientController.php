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

        // If there is an expiry date field, ensure it's not in the past
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

            // Check for duplicate appointment
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

            // Validate subscription for subscription plan
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

                // Subscription plan - no payment required
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

            // Medicare plan - payment required
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

            // Base validation
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

            // Add payment validation if not subscription
            if ($request->plan != 'subscription') {
                $validationRules['stripeToken'] = 'required|string';
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
            $check_if_exists = Appointment::where('booked_by', $userID)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->count();

            if ($check_if_exists > 0) {
                return response()->json([
                    'type' => 'error',
                    'message' => 'You already booked an appointment in the same date: ' . $request->date . ' and time: ' . $request->time
                ], 400);
            }

            // Handle insurance card uploads
            $frontInsurancePath = null;
            $backInsurancePath = null;

            if ($request->has('insurance_card_front') && $request->insurance_card_front) {
                $frontInsurancePath = $this->uploadBase64Image($request->insurance_card_front, 'frontInsurance');
            }

            if ($request->has('insurance_card_back') && $request->insurance_card_back) {
                $backInsurancePath = $this->uploadBase64Image($request->insurance_card_back, 'backInsurance');
            }

            // Generate appointment UID
            $prefix = 'SA';
            $year = date('y');
            $month = date('m');
            $currentMonthCount = Appointment::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count();
            $sequence = str_pad($currentMonthCount + 1, 4, '0', STR_PAD_LEFT);
            $appointment_uid = $prefix . $year . $month . '-' . $sequence;

            $plan = $request->plan;

            if ($plan == 'subscription') {
                // Validate subscription
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

                // Create appointment with subscription
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
                    'policyholder_name' => $request->policyholder_name ?? null,
                    'policy_id' => $request->policy_id,
                    'group_number' => $request->group_number ?? null,
                    'insurance_plan_type' => $request->insurance_plan_type,
                    'chief_complaint' => $request->chief_complaint,
                    'symptom_onset' => $request->symptom_onset,
                    'prior_diagnoses' => $request->prior_diagnoses ?? null,
                    'current_medications' => $request->current_medications,
                    'allergies' => $request->allergies,
                    'past_surgical_history' => $request->past_surgical_history,
                    'family_medical_history' => $request->family_medical_history ?? null,
                    'plan' => $plan,
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
                    'message' => 'Appointment booked successfully!',
                    'data' => [
                        'appointment_uid' => $appointment_uid,
                        'date' => $request->date,
                        'time' => $request->time,
                        'plan' => $plan
                    ]
                ], 201);
            } else {
                // Medicare plan - process payment
                $settings = Settings::first();
                if (!$settings) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'System configuration not found'
                    ], 500);
                }

                $amount = $settings->medicare_amount;
                $currency = $settings->currency;
                $stripe_secret_key = $settings->stripe_secret_key;

                Stripe\Stripe::setApiKey($stripe_secret_key);

                try {
                    // Create a Payment Intent
                    $paymentIntent = Stripe\PaymentIntent::create([
                        'amount' => $amount * 100,
                        'currency' => strtolower($currency),
                        'payment_method' => $request->stripeToken,
                        'confirm' => true,
                        'automatic_payment_methods' => [
                            'enabled' => true,
                            'allow_redirects' => 'never',
                        ],
                        'description' => 'Appointment booking for ' . $request->users_full_name,
                        'receipt_email' => $request->users_email,
                        'metadata' => [
                            'patient_id' => $patient_id,
                            'appointment_date' => $request->date,
                            'appointment_time' => $request->time,
                        ],
                    ]);

                    // Check if payment requires additional action
                    if ($paymentIntent->status === 'requires_action' || $paymentIntent->status === 'requires_source_action') {
                        return response()->json([
                            'type' => 'error',
                            'message' => 'Payment requires additional authentication',
                            'requires_action' => true,
                            'payment_intent_client_secret' => $paymentIntent->client_secret
                        ], 400);
                    }

                    // Check if payment failed
                    if ($paymentIntent->status !== 'succeeded') {
                        return response()->json([
                            'type' => 'error',
                            'message' => 'Payment failed: ' . ($paymentIntent->last_payment_error->message ?? 'Unknown error')
                        ], 400);
                    }

                    // Create appointment
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
                        'stripe_charge_id' => $paymentIntent->latest_charge,
                        'stripe_payment_intent_id' => $paymentIntent->id,
                        'payment_status' => 'completed',
                        'amount' => $amount,
                        'currency' => $currency,
                        'insurance_company' => $request->insurance_company,
                        'policyholder_name' => $request->policyholder_name ?? null,
                        'policy_id' => $request->policy_id,
                        'group_number' => $request->group_number ?? null,
                        'insurance_plan_type' => $request->insurance_plan_type,
                        'chief_complaint' => $request->chief_complaint,
                        'symptom_onset' => $request->symptom_onset,
                        'prior_diagnoses' => $request->prior_diagnoses ?? null,
                        'current_medications' => $request->current_medications,
                        'allergies' => $request->allergies,
                        'past_surgical_history' => $request->past_surgical_history,
                        'family_medical_history' => $request->family_medical_history ?? null,
                        'plan' => $plan,
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
                            'plan' => $plan,
                            'payment_details' => [
                                'amount' => $amount,
                                'currency' => $currency,
                                'charge_id' => $paymentIntent->latest_charge
                            ]
                        ]
                    ], 201);
                } catch (\Stripe\Exception\CardException $e) {
                    return response()->json([
                        'type' => 'error',
                        'message' => $e->getError()->message
                    ], 400);
                } catch (\Stripe\Exception\RateLimitException $e) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Too many requests. Please try again later.'
                    ], 429);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Invalid parameters were supplied to Stripe: ' . $e->getMessage()
                    ], 400);
                } catch (\Stripe\Exception\AuthenticationException $e) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Authentication with Stripe failed.'
                    ], 500);
                } catch (\Stripe\Exception\ApiConnectionException $e) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Network communication with Stripe failed.'
                    ], 500);
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    return response()->json([
                        'type' => 'error',
                        'message' => 'Stripe API error: ' . $e->getMessage()
                    ], 500);
                } catch (\Exception $e) {
                    return response()->json([
                        'type' => 'error',
                        'message' => $e->getMessage()
                    ], 500);
                }
            }
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

    /**
     * Handle base64 image upload for API
     * Converts base64 to file and uses the same upload method as web controller
     */
    private function uploadBase64Image($base64String, $type)
    {
        try {
            // Check if the string contains the data URL prefix
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                $extension = $matches[1];
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
            } else {
                // Try to detect extension from decoded data
                $extension = 'jpg';
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

            // Create a temporary file to mimic uploaded file behavior
            $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
            file_put_contents($tempFile, $imageData);

            // Create a mock UploadedFile object
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                'insurance_' . $type . '.' . $extension,
                mime_content_type($tempFile),
                null,
                true
            );

            // Use the same uploadFile method pattern from web controller
            $result = $this->uploadFile($uploadedFile, $type);

            // Clean up temp file
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

    /**
     * Upload file - exact same method as web controller
     */
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