<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignupTrial extends Model
{
    use HasFactory;

    protected $table = 'signup_trials';

    protected $fillable = [
        'username',
        'email',
        'OTP',
        'status',
        'trial_by',
    ];
}
