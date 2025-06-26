<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_by_provider_id',
        'appointment_uid',
        'patient_name',
        'patient_id',
        'age',
        'gender',
        'allergies',
        'drug_name',
        'strength',
        'form_manufacturer',
        'dose_amount',
        'frequency',
        'time_duration',
        'quantity',
        'refills',
        'start_date',
    ];
}
