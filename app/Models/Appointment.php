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

        // new fields from booking page
        'insurance_company',
        'policyholder_name',
        'policy_id',
        'group_number',
        'insurance_plan_type',
        'chief_complaint',
        'symptom_onset',
        'prior_diagnoses',
        'current_medications',
        'allergies',
        'past_surgical_history',
        'family_medical_history',
        'plan',
        'insurance_card_front',
        'insurance_card_back',
        // done new fields from booking page

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
