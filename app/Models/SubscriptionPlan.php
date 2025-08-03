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
    ];
}