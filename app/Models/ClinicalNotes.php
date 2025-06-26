<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicalNotes extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_by_provider_id',
        'appointment_uid',
        'chief_complaint',
        'history_of_present_illness',
        'past_medical_history',
        'medications',
        'family_history',
        'social_history',
        'physical_examination',
        'assessment_plan',
        'progress_notes',
        'provider_information',
    ];
}
