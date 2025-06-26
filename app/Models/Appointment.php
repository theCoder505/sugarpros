<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;


    protected $fillable = [
        'appointment_uid',
        'fname',
        'lname',
        'email',
        'patient_id',
        'date',
        'time',
        'meet_link',
        'booked_by',
        'provider_id',
        'type',
        'status',

        'users_full_name',
        'users_address',
        'users_email',
        'country_code',
        'users_phone',

        'stripe_charge_id',
        'payment_status',
        'amount',
        'currency',
    ];
}
