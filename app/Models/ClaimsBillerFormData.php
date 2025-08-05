<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimsBillerFormData extends Model
{
    use HasFactory;

    protected $table = 'claims_biller_form_data';

    protected $fillable = [
        'appointment_uid',
        'action',
        'name',
        'dob',
        'patient_id',
        'gender',
        'phone',
        'address',
        'coverage_type',
        'primary',
        'plan_name',
        'plan_type',
        'insurance_ID',
        'group_ID',
        'effective_date',
        'eligibility',
        'claim_address',
        'gurantor',
        'modifiers',
        'billing_code',
        'billing_text',
        'diagnoses',
        'start_date',
        'end_date',
        'units',
        'quantity',
        'billed_charge',
        'notes',
        'status',
        'done_by',
        'done_by_id',
        'claim_status',
        'claimmd_id',
        'claim_response',
    ];


    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_uid', 'appointment_uid');
    }
}
