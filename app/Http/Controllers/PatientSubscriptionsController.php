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
        $request->validate([
            'users_full_name' => 'required',
            'users_address' => 'required',
            'users_email' => 'required|email',
            'users_phone' => 'required',
            'country_code' => 'required',
        ]);

        $recurring_option = $request['recurring_option'];
        $plan = $request['plan'];
        $currency = Settings::where('id', 1)->value('currency');
        $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');

        // Set Stripe API key
        Stripe\Stripe::setApiKey($stripe_secret_key);

        // Determine price key and amount based on plan
        if ($recurring_option == 'monthly' && $plan == 'Basic') {
            $amount = Settings::where('id', 1)->value('monthly_basic_amount');
            $price_key = Settings::where('id', 1)->value('monthly_basic_price_key');
        } elseif ($recurring_option == 'monthly' && $plan == 'Premium') {
            $amount = Settings::where('id', 1)->value('monthly_premium_amount');
            $price_key = Settings::where('id', 1)->value('monthly_premium_price_key');
        } elseif ($recurring_option == 'monthly' && $plan == 'VIP') {
            $amount = Settings::where('id', 1)->value('monthly_vip_amount');
            $price_key = Settings::where('id', 1)->value('monthly_vip_price_key');
        } elseif ($recurring_option == 'annually' && $plan == 'Basic') {
            $amount = Settings::where('id', 1)->value('annual_basic_amount');
            $price_key = Settings::where('id', 1)->value('annual_basic_price_key');
        } elseif ($recurring_option == 'annually' && $plan == 'Premium') {
            $amount = Settings::where('id', 1)->value('annual_premium_amount');
            $price_key = Settings::where('id', 1)->value('annual_premium_price_key');
        } elseif ($recurring_option == 'annually' && $plan == 'VIP') {
            $amount = Settings::where('id', 1)->value('annual_vip_amount');
            $price_key = Settings::where('id', 1)->value('annual_vip_price_key');
        }

        try {
            // Check for existing subscription
            $current_subscription = SubscriptionPlan::where('availed_by_uid', Auth::user()->patient_id)
                ->whereIn('stripe_status', ['active', 'trialing'])
                ->first();

            if ($current_subscription) {
                try {
                    // Try to retrieve Stripe subscription
                    $stripe_subscription = Stripe\Subscription::retrieve($current_subscription->stripe_charge_id);
                    
                    // If found, handle subscription change
                    return $this->handleSubscriptionChange(
                        $current_subscription,
                        $request,
                        $recurring_option,
                        $plan,
                        $price_key,
                        $amount
                    );
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // If Stripe subscription not found, create new one
                    return $this->createNewSubscription(
                        $request,
                        $recurring_option,
                        $plan,
                        $price_key,
                        $amount
                    );
                }
            } else {
                // Create new subscription
                return $this->createNewSubscription(
                    $request,
                    $recurring_option,
                    $plan,
                    $price_key,
                    $amount
                );
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function createNewSubscription($request, $recurring_option, $plan, $price_key, $amount)
    {
        $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');
        Stripe\Stripe::setApiKey($stripe_secret_key);

        // Create Stripe customer
        $customer = Stripe\Customer::create([
            'name' => $request->users_full_name . ' | PatientID: ' . Auth::user()->patient_id,
            'email' => $request->users_email,
            'phone' => $request->users_phone,
            'address' => [
                'line1' => $request->users_address,
            ],
            'source' => $request->stripeToken,
        ]);

        // Create subscription
        $subscription = Stripe\Subscription::create([
            'customer' => $customer->id,
            'items' => [
                ['price' => $price_key],
            ],
            'expand' => ['latest_invoice.payment_intent'],
        ]);

        // Save subscription to database
        $subscriptionPlan = SubscriptionPlan::create([
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

        Notification::create([
            'user_id' => Auth::user()->patient_id,
            'user_type' => 'patient',
            'notification' => 'Your new subscription for <b>' . $recurring_option . ' ' . $plan . '</b> has been successfully created.',
            'read_status' => 0,
            'read_time' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully!',
            'subscription_id' => $subscription->id,
        ]);
    }

    protected function handleSubscriptionChange($current_subscription, $request, $new_recurring_option, $new_plan, $new_price_key, $new_amount)
    {
        $stripe_secret_key = Settings::where('id', 1)->value('stripe_secret_key');
        Stripe\Stripe::setApiKey($stripe_secret_key);

        // Update existing Stripe subscription
        $stripe_subscription = Stripe\Subscription::retrieve($current_subscription->stripe_charge_id);

        // Update subscription items
        $updated_subscription = Stripe\Subscription::update($current_subscription->stripe_charge_id, [
            'cancel_at_period_end' => false,
            'items' => [
                [
                    'id' => $stripe_subscription->items->data[0]->id,
                    'price' => $new_price_key,
                ],
            ],
            'proration_behavior' => 'create_prorations',
        ]);

        // Update local database record
        $current_subscription->update([
            'recurring_option' => $new_recurring_option,
            'plan' => $new_plan,
            'amount' => $new_amount,
            'stripe_status' => $updated_subscription->status,
            'last_recurrent_date' => now(),
        ]);

        // Create notification about the change
        Notification::create([
            'user_id' => Auth::user()->patient_id,
            'user_type' => 'patient',
            'notification' => 'Your subscription has been updated to <b>' . $new_recurring_option . ' ' . $new_plan . '</b>. Changes will take effect immediately.',
            'read_status' => 0,
            'read_time' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully!',
            'subscription_id' => $updated_subscription->id,
        ]);
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
                    'notification' => 'Your ' . $subscription->recurring_option . ' ' . $subscription->plan .
                        ' subscription has been renewed. Next renewal: ' . $newDate->format('M j, Y'),
                    'read_status' => 0,
                    'read_time' => null,
                ]);

                $updatedCount++;
            } catch (\Exception $e) {
                Log::error("Failed to update subscription {$subscription->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Subscription cron job executed successfully.',
            'updated_subscriptions' => $updatedCount,
            'total_processed' => $subscriptions->count()
        ]);
    }
}
