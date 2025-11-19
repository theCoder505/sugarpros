<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Notification;
use App\Models\Settings;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe;

class PatientSubscriptionsController extends Controller
{
    public function subscriptions()
    {
        $allFaqs = Faq::orderBy('id', 'ASC')->get();
        $stripe_amount = Settings::where('id', 1)->value('stripe_amount');
        $medicare_amount = Settings::where('id', 1)->value('medicare_amount');
        $monthly_basic_amount = Settings::where('id', 1)->value('monthly_basic_amount');
        $monthly_premium_amount = Settings::where('id', 1)->value('monthly_premium_amount');
        $monthly_vip_amount = Settings::where('id', 1)->value('monthly_vip_amount');
        $annual_basic_amount = Settings::where('id', 1)->value('annual_basic_amount');
        $annual_premium_amount = Settings::where('id', 1)->value('annual_premium_amount');
        $annual_vip_amount = Settings::where('id', 1)->value('annual_vip_amount');

        // Get current active subscription (where last_recurrent_date is in the future)
        $current_subscription = SubscriptionPlan::where('availed_by_uid', Auth::user()->patient_id)
            ->where('stripe_status', 'paid')
            ->where('last_recurrent_date', '>', now())
            ->orderBy('last_recurrent_date', 'desc')
            ->first();

        $current_recurring_option = $current_subscription->recurring_option ?? null;
        $current_plan = $current_subscription->plan ?? null;

        return view('patient.subscriptions', compact(
            'allFaqs',
            'stripe_amount',
            'medicare_amount',
            'monthly_basic_amount',
            'monthly_premium_amount',
            'monthly_vip_amount',
            'annual_basic_amount',
            'annual_premium_amount',
            'annual_vip_amount',
            'current_recurring_option',
            'current_plan',
            'current_subscription'
        ));
    }

    public function subscriptionPlan($recurring_option, $plan)
    {
        $stripe_client_id = Settings::where('id', 1)->value('stripe_client_id');
        $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');
        $prefixcodes = Settings::where('id', 1)->value('prefixcode');

        return view('patient.subscriptionPlan', compact(
            'plan',
            'stripe_client_id',
            'recurring_option',
            'stripe_secret_key',
            'prefixcodes'
        ));
    }

    public function completeSubscription(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'users_full_name' => 'required|string|max:255',
                'users_address' => 'required|string|max:500',
                'users_email' => 'required|email|max:255',
                'users_phone' => 'required|string|max:20',
                'country_code' => 'required|string|max:10',
                'stripeToken' => 'required|string',
                'recurring_option' => 'required|in:monthly,annually',
                'plan' => 'nullable|string|max:255',
            ]);

            $recurring_option = $request->recurring_option;
            $plan = $request->plan ?? 'premium'; // Default to premium if no plan specified

            // Get Stripe secret key
            $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');

            // Validate Stripe key configuration
            if (empty($stripe_secret_key)) {
                $this->storePaymentError($request, 'Stripe is not configured. Please contact support.', 0);
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe is not configured. Please contact support.',
                    'redirect' => route('subscription.cancel')
                ], 500);
            }

            // Set Stripe API key
            Stripe\Stripe::setApiKey($stripe_secret_key);

            // Get amount based on recurring option only
            $amount = $this->getAmountByRecurringOption($recurring_option);

            if ($amount == null || $amount <= 0) {
                $this->storePaymentError($request, 'Amount not configured for this subscription. Please contact support.', 0);
                return response()->json([
                    'success' => false,
                    'message' => 'Amount not configured for this subscription. Please contact support.',
                    'redirect' => route('subscription.cancel')
                ], 400);
            }

            // Check if user has an existing active subscription
            $current_subscription = SubscriptionPlan::where('availed_by_uid', Auth::user()->patient_id)
                ->where('stripe_status', 'paid')
                ->where('last_recurrent_date', '>', now())
                ->first();

            // If there's an active subscription, mark it as replaced
            if ($current_subscription) {
                $current_subscription->update([
                    'stripe_status' => 'replaced',
                    'last_recurrent_date' => now()
                ]);

                Log::info('Previous subscription replaced', [
                    'patient_id' => Auth::user()->patient_id,
                    'old_plan' => $current_subscription->plan,
                    'new_plan' => $plan
                ]);
            }

            // Create direct payment
            return $this->createDirectPayment(
                $request,
                $recurring_option,
                $plan,
                $amount
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error: ' . json_encode($e->errors()));

            $this->storePaymentError($request, 'Validation failed: ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())), 0);

            return response()->json([
                'success' => false,
                'redirect' => route('subscription.cancel'),
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())),
            ], 422);
        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe Card Error: ' . $e->getMessage(), [
                'patient_id' => Auth::user()->patient_id ?? 'unknown'
            ]);

            $amount = $this->getAmountByRecurringOption($request->recurring_option) ?? 0;

            $this->storePaymentError($request, $e->getError()->message, $amount);

            return response()->json([
                'success' => false,
                'redirect' => route('subscription.cancel'),
                'message' => 'Card error: ' . $e->getError()->message,
            ], 400);
        } catch (\Stripe\Exception\RateLimitException $e) {
            Log::error('Stripe Rate Limit: ' . $e->getMessage());

            $this->storePaymentError($request, 'Too many requests. Please try again later.', 0);

            return response()->json([
                'success' => false,
                'redirect' => route('subscription.cancel'),
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe Invalid Request: ' . $e->getMessage(), [
                'patient_id' => Auth::user()->patient_id ?? 'unknown'
            ]);

            $errorMessage = 'Invalid request: ' . $e->getError()->message;

            if (str_contains($e->getMessage(), 'No such token')) {
                $errorMessage = 'Invalid payment token. Please refresh the page and try again.';
            }

            $this->storePaymentError($request, $errorMessage, 0);

            return response()->json([
                'success' => false,
                'redirect' => route('subscription.cancel'),
                'message' => $errorMessage,
            ], 400);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe Authentication Error: ' . $e->getMessage());

            $this->storePaymentError($request, 'Authentication failed. Please contact support.', 0);

            return response()->json([
                'success' => false,
                'redirect' => route('subscription.cancel'),
                'message' => 'Authentication failed. Please contact support.',
            ], 500);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error('Stripe Connection Error: ' . $e->getMessage());

            $this->storePaymentError($request, 'Network error connecting to payment processor. Please check your connection and try again.', 0);

            return response()->json([
                'success' => false,
                'redirect' => route('subscription.cancel'),
                'message' => 'Network error connecting to payment processor. Please check your connection and try again.',
            ], 503);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'patient_id' => Auth::user()->patient_id ?? 'unknown'
            ]);

            $this->storePaymentError($request, 'Payment processor error: ' . $e->getError()->message, 0);

            return response()->json([
                'success' => false,
                'redirect' => route('subscription.cancel'),
                'message' => 'Payment processor error: ' . $e->getError()->message,
            ], 500);
        } catch (\Exception $e) {
            $this->storePaymentError($request, 'An unexpected error occurred. Please try again or contact support.', 0);
            return response()->json([
                'success' => false,
                'reason' => $e->getMessage(),
                'redirect' => route('subscription.cancel'),
                'message' => 'An unexpected error occurred. Please try again or contact support.',
            ], 500);
        }
    }

    /**
     * Store payment error details in session for cancel page
     */
    protected function storePaymentError(Request $request, string $errorMessage, float $amount)
    {
        session([
            'payment_error' => [
                'plan' => $request->plan ?? 'N/A',
                'recurring_option' => $request->recurring_option ?? 'N/A',
                'amount' => $amount,
                'error_message' => $errorMessage
            ]
        ]);
    }

    /**
     * Get amount based on recurring option only (ignores plan)
     */
    protected function getAmountByRecurringOption($recurring_option)
    {
        if ($recurring_option == 'monthly') {
            return Settings::where('id', 1)->value('monthly_premium_amount');
        } else {
            return Settings::where('id', 1)->value('annual_premium_amount');
        }
    }

    protected function getPriceData($recurring_option, $plan)
    {
        $setting_map = [
            'monthly_Basic' => 'monthly_basic_amount',
            'monthly_premium' => 'monthly_premium_amount',
            'monthly_VIP' => 'monthly_vip_amount',
            'annually_Basic' => 'annual_basic_amount',
            'annually_premium' => 'annual_premium_amount',
            'annually_VIP' => 'annual_vip_amount',
        ];

        $key = $recurring_option . '_' . $plan;

        if (!isset($setting_map[$key])) {
            return ['success' => false, 'message' => 'Invalid plan selected'];
        }

        $amount = Settings::where('id', 1)->value($setting_map[$key]);

        if (empty($amount) || $amount <= 0) {
            return ['success' => false, 'message' => 'Amount not configured for this plan. Please contact support.'];
        }

        return [
            'success' => true,
            'amount' => $amount
        ];
    }

    protected function createDirectPayment($request, $recurring_option, $plan, $amount)
    {
        try {
            $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');
            $currency = Settings::where('id', 1)->value('currency');

            Stripe\Stripe::setApiKey($stripe_secret_key);

            // Create one-time direct charge
            $charge = Stripe\Charge::create([
                'amount' => $amount * 100, // Amount in cents
                'currency' => strtolower($currency),
                'source' => $request->stripeToken,
                'description' => ucfirst($recurring_option) . ' ' . $plan . ' Plan - Patient ID: ' . Auth::user()->patient_id,
                'receipt_email' => $request->users_email,
                'metadata' => [
                    'patient_id' => Auth::user()->patient_id,
                    'user_id' => Auth::id(),
                    'patient_name' => $request->users_full_name,
                    'patient_email' => $request->users_email,
                    'patient_phone' => $request->country_code . $request->users_phone,
                    'plan' => $plan,
                    'recurring_option' => $recurring_option,
                    'payment_type' => 'direct_payment'
                ]
            ]);

            // Calculate expiration date
            $expiration_date = $recurring_option == 'monthly'
                ? now()->addMonth()
                : now()->addYear();

            // Save subscription to database - FIX: Use empty string for stripe_customer_id
            SubscriptionPlan::create([
                'availed_by_uid' => Auth::user()->patient_id,
                'recurring_option' => $recurring_option,
                'plan' => $plan,
                'users_full_name' => $request->users_full_name,
                'users_address' => $request->users_address,
                'users_email' => $request->users_email,
                'users_phone' => $request->users_phone,
                'country_code' => $request->country_code,
                'amount' => $amount,
                'stripe_charge_id' => $charge->id,
                'stripe_customer_id' => '', // Use empty string instead of null
                'stripe_status' => 'paid',
                'last_recurrent_date' => $expiration_date,
            ]);

            // Create notification
            Notification::create([
                'user_id' => Auth::user()->patient_id,
                'user_type' => 'patient',
                'notification' => 'Payment successful! Your <b>' . ucfirst($recurring_option) . ' ' . $plan . '</b> plan is now active until ' . $expiration_date->format('M j, Y') . '.',
                'read_status' => 0,
                'read_time' => null,
            ]);

            Log::info('Direct payment successful', [
                'patient_id' => Auth::user()->patient_id,
                'charge_id' => $charge->id,
                'plan' => $plan,
                'recurring_option' => $recurring_option,
                'amount' => $amount,
                'expires_at' => $expiration_date->toDateTimeString()
            ]);

            // Store success data in session
            session([
                'payment_success' => [
                    'plan' => $plan,
                    'recurring_option' => $recurring_option,
                    'amount' => $amount,
                    'transaction_id' => $charge->id,
                    'expires_at' => $expiration_date->format('M j, Y')
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment successful! Your subscription is now active.',
                'redirect' => route('subscription.success')
            ]);
        } catch (\Exception $e) {
            Log::error('Direct Payment Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'patient_id' => Auth::user()->patient_id ?? 'unknown',
                'plan' => $plan,
                'recurring_option' => $recurring_option,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function subscriptionSuccess()
    {
        // Retrieve payment data from session
        $paymentData = session('payment_success');

        if (!$paymentData) {
            // If no session data, redirect to subscriptions page
            return redirect()->route('patient.subscriptions')->with('error', 'No payment data found.');
        }

        $plan = $paymentData['plan'] ?? 'N/A';
        $recurring_option = $paymentData['recurring_option'] ?? 'N/A';
        $amount = $paymentData['amount'] ?? 0;
        $transaction_id = $paymentData['transaction_id'] ?? 'N/A';
        $expires_at = $paymentData['expires_at'] ?? 'N/A';

        // Clear the session data after retrieving it
        session()->forget('payment_success');

        return view('patient.subscriptionSuccess', compact(
            'plan',
            'recurring_option',
            'amount',
            'transaction_id',
            'expires_at'
        ));
    }

    public function subscriptionCancel()
    {
        // Retrieve payment error data from session
        $paymentError = session('payment_error');

        if (!$paymentError) {
            // If no session data, use default values or redirect
            return redirect()->route('patient.subscriptions')->with('error', 'No payment error data found.');
        }

        $plan = $paymentError['plan'] ?? 'N/A';
        $recurring_option = $paymentError['recurring_option'] ?? 'N/A';
        $amount = $paymentError['amount'] ?? 0;
        $error_message = $paymentError['error_message'] ?? 'Payment failed';
        $transaction_id = 'FAILED-' . now()->format('Y-m-d') . '-' . rand(1000, 9999);

        // Clear the session data after retrieving it
        session()->forget('payment_error');

        return view('patient.subscriptionCancel', compact(
            'plan',
            'recurring_option',
            'amount',
            'transaction_id',
            'error_message'
        ));
    }
}
