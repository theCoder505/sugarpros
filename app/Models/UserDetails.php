<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'fname',
        'mname',
        'lname',
        'dob',
        'gender',
        'email',
        'phone',
        'street',
        'city',
        'state',
        'zip_code',
        'medicare_number',
        'group_number',
        'license',
        'ssn',
        'notification_type',
        'insurance_provider',
        'insurance_plan_number',
        'insurance_group_number',
    ];
}
