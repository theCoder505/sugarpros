<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class BillerAdmin extends Authenticatable
{
    use HasFactory;

    protected $table = 'biller_admins';

    protected $fillable = [
        'biller_admin_id',
        'biller_name',
        'biller_email',
        'password',
        'remember_token',
        'last_login_time', // as datetime
    ];

    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value;
    }
}
