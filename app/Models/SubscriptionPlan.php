<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'availed_by_uid',
        'recurring_option',
        'plan',
        'users_full_name',
        'users_address',
        'users_email',
        'users_phone',
        'country_code',
        'amount',
        'stripe_charge_id',
        'last_recurrent_date',
        'stripe_customer_id',
        'stripe_status'
    ];

    protected $casts = [
        'last_recurrent_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the subscription start date (same as created_at)
     */
    public function getStartDateAttribute()
    {
        return $this->created_at;
    }

    /**
     * Get the subscription expiration date (same as last_recurrent_date)
     */
    public function getExpirationDateAttribute()
    {
        return $this->last_recurrent_date;
    }

    /**
     * Check if subscription is currently active
     */
    public function isActive()
    {
        return $this->stripe_status === 'paid' && $this->last_recurrent_date > now();
    }

    /**
     * Check if subscription has expired
     */
    public function isExpired()
    {
        return $this->stripe_status === 'expired' || $this->last_recurrent_date <= now();
    }

    /**
     * Get days remaining until expiration
     */
    public function daysRemaining()
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return now()->diffInDays($this->last_recurrent_date, false);
    }

    /**
     * Scope to get only active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('stripe_status', 'paid')
                    ->where('last_recurrent_date', '>', now());
    }

    /**
     * Scope to get expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('stripe_status', 'expired')
              ->orWhere(function($subQuery) {
                  $subQuery->where('stripe_status', 'paid')
                           ->where('last_recurrent_date', '<=', now());
              });
        });
    }
}