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

        $current_subscription = SubscriptionPlan::where('availed_by_uid', Auth::user()->patient_id)
            ->whereIn('stripe_status', ['active', 'trialing'])
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
                'plan' => 'required|in:Basic,Premium,VIP',
            ]);

            $recurring_option = $request->recurring_option;
            $plan = $request->plan;
            
            // Get Stripe secret key
            $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');

            // Validate Stripe key configuration
            if (empty($stripe_secret_key)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe is not configured. Please contact support.',
                ], 500);
            }

            // Set Stripe API key
            Stripe\Stripe::setApiKey($stripe_secret_key);

            // Get price data
            $price_data = $this->getPriceData($recurring_option, $plan);
            if (!$price_data['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $price_data['message'],
                ], 400);
            }

            $amount = $price_data['amount'];
            $price_key = $price_data['price_key'];

            // Check if user has an existing active subscription
            $current_subscription = SubscriptionPlan::where('availed_by_uid', Auth::user()->patient_id)
                ->whereIn('stripe_status', ['active', 'trialing'])
                ->first();

            // If there's an existing subscription, verify it with Stripe
            if ($current_subscription) {
                // Check if it's the same plan
                $is_same_plan = ($current_subscription->recurring_option === $recurring_option) 
                    && ($current_subscription->plan === $plan);

                if ($is_same_plan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You are already subscribed to this plan.',
                    ], 400);
                }

                // Verify subscription exists in Stripe and is valid
                try {
                    $stripe_subscription = Stripe\Subscription::retrieve($current_subscription->stripe_charge_id);
                    
                    // Check if subscription is in a state that can be updated
                    if (in_array($stripe_subscription->status, ['active', 'trialing', 'past_due'])) {
                        // Subscription is valid, proceed with update
                        return $this->handleSubscriptionChange(
                            $current_subscription,
                            $request,
                            $recurring_option,
                            $plan,
                            $price_key,
                            $amount
                        );
                    } else {
                        // Subscription exists but is canceled/incomplete - cancel local record and create new
                        Log::warning('Stripe subscription found but not active, creating new subscription', [
                            'patient_id' => Auth::user()->patient_id,
                            'old_subscription_id' => $current_subscription->stripe_charge_id,
                            'stripe_status' => $stripe_subscription->status
                        ]);

                        // Mark old subscription as canceled
                        $current_subscription->update([
                            'stripe_status' => 'canceled',
                            'ends_at' => now()
                        ]);

                        // Create new subscription
                        return $this->createNewSubscription(
                            $request,
                            $recurring_option,
                            $plan,
                            $price_key,
                            $amount,
                            true // Indicate this is a renewal
                        );
                    }

                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Stripe subscription not found - cancel local record and create new one
                    Log::warning('Stripe subscription not found, creating new subscription', [
                        'patient_id' => Auth::user()->patient_id,
                        'old_subscription_id' => $current_subscription->stripe_charge_id,
                        'error' => $e->getMessage()
                    ]);

                    // Mark old subscription as canceled
                    $current_subscription->update([
                        'stripe_status' => 'canceled',
                        'ends_at' => now()
                    ]);

                    // Create new subscription
                    return $this->createNewSubscription(
                        $request,
                        $recurring_option,
                        $plan,
                        $price_key,
                        $amount,
                        true // Indicate this is a renewal
                    );

                } catch (\Stripe\Exception\ApiErrorException $e) {
                    // Other Stripe API errors
                    Log::error('Stripe API error during subscription verification', [
                        'patient_id' => Auth::user()->patient_id,
                        'error' => $e->getMessage()
                    ]);

                    throw $e; // Re-throw to be caught by outer catch block
                }
            } else {
                // No existing subscription, create new one
                return $this->createNewSubscription(
                    $request,
                    $recurring_option,
                    $plan,
                    $price_key,
                    $amount,
                    false
                );
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_map(fn($err) => implode(', ', $err), $e->errors())),
            ], 422);
        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe Card Error: ' . $e->getMessage(), [
                'patient_id' => Auth::user()->patient_id ?? 'unknown'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Card error: ' . $e->getError()->message,
            ], 400);
        } catch (\Stripe\Exception\RateLimitException $e) {
            Log::error('Stripe Rate Limit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Too many requests. Please try again later.',
            ], 429);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error('Stripe Invalid Request: ' . $e->getMessage(), [
                'patient_id' => Auth::user()->patient_id ?? 'unknown'
            ]);
            
            // Handle specific error for invalid token
            if (str_contains($e->getMessage(), 'No such token')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment token. Please refresh the page and try again.',
                ], 400);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid request: ' . $e->getError()->message,
            ], 400);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            Log::error('Stripe Authentication Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Authentication failed. Please contact support.',
            ], 500);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            Log::error('Stripe Connection Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Network error connecting to payment processor. Please check your connection and try again.',
            ], 503);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe API Error: ' . $e->getMessage(), [
                'patient_id' => Auth::user()->patient_id ?? 'unknown'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Payment processor error: ' . $e->getError()->message,
            ], 500);
        } catch (\Exception $e) {
            Log::error('Subscription Error: ' . $e->getMessage(), [
                'user_id' => Auth::id() ?? 'unknown',
                'patient_id' => Auth::user()->patient_id ?? 'unknown',
                'plan' => $plan ?? 'N/A',
                'recurring_option' => $recurring_option ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again or contact support.',
            ], 500);
        }
    }

    protected function getPriceData($recurring_option, $plan)
    {
        $setting_map = [
            'monthly_Basic' => ['amount' => 'monthly_basic_amount', 'price_key' => 'monthly_basic_price_key'],
            'monthly_Premium' => ['amount' => 'monthly_premium_amount', 'price_key' => 'monthly_premium_price_key'],
            'monthly_VIP' => ['amount' => 'monthly_vip_amount', 'price_key' => 'monthly_vip_price_key'],
            'annually_Basic' => ['amount' => 'annual_basic_amount', 'price_key' => 'annual_basic_price_key'],
            'annually_Premium' => ['amount' => 'annual_premium_amount', 'price_key' => 'annual_premium_price_key'],
            'annually_VIP' => ['amount' => 'annual_vip_amount', 'price_key' => 'annual_vip_price_key'],
        ];

        $key = $recurring_option . '_' . $plan;

        if (!isset($setting_map[$key])) {
            return ['success' => false, 'message' => 'Invalid plan selected'];
        }

        $amount = Settings::where('id', 1)->value($setting_map[$key]['amount']);
        $price_key = Settings::where('id', 1)->value($setting_map[$key]['price_key']);

        if (empty($price_key)) {
            return ['success' => false, 'message' => 'Price key not configured for this plan. Please contact support.'];
        }

        if (empty($amount) || $amount <= 0) {
            return ['success' => false, 'message' => 'Amount not configured for this plan. Please contact support.'];
        }

        return [
            'success' => true,
            'amount' => $amount,
            'price_key' => $price_key
        ];
    }

    protected function createNewSubscription($request, $recurring_option, $plan, $price_key, $amount, $is_renewal = false)
    {
        try {
            $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');
            Stripe\Stripe::setApiKey($stripe_secret_key);

            // Create Stripe customer
            $customer = Stripe\Customer::create([
                'name' => $request->users_full_name . ' | PatientID: ' . Auth::user()->patient_id,
                'email' => $request->users_email,
                'phone' => $request->country_code . $request->users_phone,
                'address' => [
                    'line1' => $request->users_address,
                ],
                'source' => $request->stripeToken,
                'metadata' => [
                    'patient_id' => Auth::user()->patient_id,
                    'user_id' => Auth::id(),
                    'plan' => $plan,
                    'recurring_option' => $recurring_option
                ]
            ]);

            // Create subscription
            $subscription = Stripe\Subscription::create([
                'customer' => $customer->id,
                'items' => [
                    ['price' => $price_key],
                ],
                'expand' => ['latest_invoice.payment_intent'],
                'metadata' => [
                    'patient_id' => Auth::user()->patient_id,
                    'user_id' => Auth::id(),
                    'plan' => $plan,
                    'recurring_option' => $recurring_option
                ]
            ]);

            // Save subscription to database
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
                'stripe_charge_id' => $subscription->id,
                'last_recurrent_date' => now(),
                'stripe_customer_id' => $customer->id,
                'stripe_status' => $subscription->status,
            ]);

            // Create notification
            $notification_message = $is_renewal 
                ? 'Your subscription has been automatically renewed! New subscription for <b>' . ucfirst($recurring_option) . ' ' . $plan . '</b> has been created.'
                : 'Your new subscription for <b>' . ucfirst($recurring_option) . ' ' . $plan . '</b> has been successfully created.';

            Notification::create([
                'user_id' => Auth::user()->patient_id,
                'user_type' => 'patient',
                'notification' => $notification_message,
                'read_status' => 0,
                'read_time' => null,
            ]);

            Log::info('New subscription created successfully', [
                'patient_id' => Auth::user()->patient_id,
                'subscription_id' => $subscription->id,
                'customer_id' => $customer->id,
                'plan' => $plan,
                'recurring_option' => $recurring_option,
                'is_renewal' => $is_renewal
            ]);

            $message = $is_renewal 
                ? 'Your previous subscription was not valid. A new subscription has been created successfully!'
                : 'Subscription created successfully!';

            return response()->json([
                'success' => true,
                'message' => $message,
                'subscription_id' => $subscription->id,
                'is_renewal' => $is_renewal
            ]);

        } catch (\Exception $e) {
            Log::error('Create Subscription Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'patient_id' => Auth::user()->patient_id ?? 'unknown',
                'plan' => $plan,
                'recurring_option' => $recurring_option,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function handleSubscriptionChange($current_subscription, $request, $new_recurring_option, $new_plan, $new_price_key, $new_amount)
    {
        try {
            $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');
            Stripe\Stripe::setApiKey($stripe_secret_key);

            // Retrieve existing Stripe subscription
            $stripe_subscription = Stripe\Subscription::retrieve($current_subscription->stripe_charge_id);

            // Double-check subscription status before updating
            if (!in_array($stripe_subscription->status, ['active', 'trialing', 'past_due'])) {
                Log::warning('Attempted to update non-active subscription', [
                    'patient_id' => Auth::user()->patient_id,
                    'subscription_id' => $current_subscription->stripe_charge_id,
                    'status' => $stripe_subscription->status
                ]);

                // Cancel local subscription and create new one
                $current_subscription->update([
                    'stripe_status' => $stripe_subscription->status,
                    'ends_at' => now()
                ]);

                return $this->createNewSubscription(
                    $request,
                    $new_recurring_option,
                    $new_plan,
                    $new_price_key,
                    $new_amount,
                    true
                );
            }

            // Update subscription with new plan
            $updated_subscription = Stripe\Subscription::update($current_subscription->stripe_charge_id, [
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $stripe_subscription->items->data[0]->id,
                        'price' => $new_price_key,
                    ],
                ],
                'proration_behavior' => 'create_prorations',
                'metadata' => [
                    'patient_id' => Auth::user()->patient_id,
                    'user_id' => Auth::id(),
                    'plan' => $new_plan,
                    'recurring_option' => $new_recurring_option,
                    'updated_at' => now()->toDateTimeString()
                ]
            ]);

            // Update local database record
            $current_subscription->update([
                'recurring_option' => $new_recurring_option,
                'plan' => $new_plan,
                'amount' => $new_amount,
                'stripe_status' => $updated_subscription->status,
                'last_recurrent_date' => now(),
                'users_full_name' => $request->users_full_name,
                'users_address' => $request->users_address,
                'users_email' => $request->users_email,
                'users_phone' => $request->users_phone,
                'country_code' => $request->country_code,
            ]);

            // Create notification
            Notification::create([
                'user_id' => Auth::user()->patient_id,
                'user_type' => 'patient',
                'notification' => 'Your subscription has been updated to <b>' . ucfirst($new_recurring_option) . ' ' . $new_plan . '</b>. Changes will take effect immediately.',
                'read_status' => 0,
                'read_time' => null,
            ]);

            Log::info('Subscription updated successfully', [
                'patient_id' => Auth::user()->patient_id,
                'subscription_id' => $updated_subscription->id,
                'old_plan' => $current_subscription->plan,
                'new_plan' => $new_plan,
                'old_recurring' => $current_subscription->recurring_option,
                'new_recurring' => $new_recurring_option
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Subscription updated successfully!',
                'subscription_id' => $updated_subscription->id,
            ]);

        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // If we get a cancellation error, it means the subscription was canceled
            // Cancel local record and create new subscription
            if (str_contains($e->getMessage(), 'canceled subscription')) {
                Log::warning('Attempted to update canceled subscription, creating new one', [
                    'patient_id' => Auth::user()->patient_id,
                    'subscription_id' => $current_subscription->stripe_charge_id,
                    'error' => $e->getMessage()
                ]);

                // Mark old subscription as canceled
                $current_subscription->update([
                    'stripe_status' => 'canceled',
                    'ends_at' => now()
                ]);

                // Create new subscription
                return $this->createNewSubscription(
                    $request,
                    $new_recurring_option,
                    $new_plan,
                    $new_price_key,
                    $new_amount,
                    true
                );
            }

            // Re-throw other errors
            throw $e;

        } catch (\Exception $e) {
            Log::error('Update Subscription Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'patient_id' => Auth::user()->patient_id ?? 'unknown',
                'subscription_id' => $current_subscription->stripe_charge_id ?? 'N/A',
                'new_plan' => $new_plan,
                'new_recurring' => $new_recurring_option,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function subscriptionSuccess()
    {
        return view('patient.subscriptionSuccess');
    }

    public function subscriptionCancel()
    {
        return view('patient.subscriptionCancel');
    }

    public function subscriptionCronJob()
    {
        $now = now();
        $updatedCount = 0;
        $failedCount = 0;

        // Get all active subscriptions that need renewal
        $subscriptions = SubscriptionPlan::where('stripe_status', 'active')
            ->where(function ($query) use ($now) {
                // Monthly subscriptions (renew every 30 days)
                $query->where(function ($q) use ($now) {
                    $q->where('recurring_option', 'monthly')
                        ->where('last_recurrent_date', '<=', $now->copy()->subDays(30));
                })
                // Annual subscriptions (renew every 365 days)
                ->orWhere(function ($q) use ($now) {
                    $q->where('recurring_option', 'annually')
                        ->where('last_recurrent_date', '<=', $now->copy()->subDays(365));
                });
            })
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                // Verify subscription still exists in Stripe
                $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');
                Stripe\Stripe::setApiKey($stripe_secret_key);

                $stripe_subscription = Stripe\Subscription::retrieve($subscription->stripe_charge_id);

                // Check if subscription is still active in Stripe
                if (!in_array($stripe_subscription->status, ['active', 'trialing'])) {
                    Log::warning('Cron: Subscription not active in Stripe', [
                        'subscription_id' => $subscription->id,
                        'stripe_status' => $stripe_subscription->status
                    ]);

                    // Update local status
                    $subscription->update([
                        'stripe_status' => $stripe_subscription->status
                    ]);

                    $failedCount++;
                    continue;
                }

                // Calculate new renewal date based on subscription type
                $newDate = $subscription->recurring_option == 'monthly'
                    ? $subscription->last_recurrent_date->addMonth()
                    : $subscription->last_recurrent_date->addYear();

                // Update the subscription
                $subscription->update([
                    'last_recurrent_date' => $newDate
                ]);

                // Create renewal notification
                Notification::create([
                    'user_id' => $subscription->availed_by_uid,
                    'user_type' => 'patient',
                    'notification' => 'Your ' . ucfirst($subscription->recurring_option) . ' ' . $subscription->plan .
                        ' subscription has been renewed. Next renewal: ' . $newDate->format('M j, Y'),
                    'read_status' => 0,
                    'read_time' => null,
                ]);

                $updatedCount++;

            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Subscription not found in Stripe
                Log::error("Cron: Stripe subscription not found for subscription {$subscription->id}", [
                    'stripe_charge_id' => $subscription->stripe_charge_id,
                    'error' => $e->getMessage()
                ]);

                // Mark as canceled
                $subscription->update([
                    'stripe_status' => 'canceled',
                    'ends_at' => now()
                ]);

                $failedCount++;

            } catch (\Exception $e) {
                Log::error("Cron: Failed to update subscription {$subscription->id}", [
                    'error' => $e->getMessage()
                ]);
                $failedCount++;
            }
        }

        return response()->json([
            'message' => 'Subscription cron job executed successfully.',
            'updated_subscriptions' => $updatedCount,
            'failed_subscriptions' => $failedCount,
            'total_processed' => $subscriptions->count()
        ]);
    }
}