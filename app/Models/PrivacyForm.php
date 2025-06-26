<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyForm extends Model
{
    use HasFactory;

    protected $table = 'privacy_forms';

    protected $fillable = [
        'user_id',
        'fname',
        'lname',
        'date',
        'users_message',
        'notice_of_privacy_practice',
        'patients_name',
        'representatives_name',
        'service_taken_date',
        'relation_with_patient',
    ];
}
