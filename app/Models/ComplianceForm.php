<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceForm extends Model
{
    use HasFactory;

    protected $table = 'compliance_forms';

    protected $fillable = [
        'user_id',
        'patients_name',
        'dob',
        'patients_signature',
        'patients_dob',
        'representative_signature',
        'representative_dob',
        'nature_with_patient',
    ];
}
