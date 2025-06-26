<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestLab extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_by_provider_id',
        'appointment_uid',
        'test_name',
        'test_code',
        'category',
        'specimen_type',
        'urgency',
        'preferred_lab_location',
        'date',
        'time',
        'patient_name',
        'patient_id',
        'clinical_notes',
        'patient_phone_no',
        'insurance_provider',
        'estimated_cost',
    ];
}
