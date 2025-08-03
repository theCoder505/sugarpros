<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'name',
        'prefix_code',
        'mobile',
        'email',
        'forget_otp',
        'password',
        'profile_picture',
        'language',
        'hippa_consent',
        'last_logged_in',
        'last_activity',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            // You can add any custom claims here
            'user_type' => 'patient', // Example custom claim
            'patient_id' => $this->patient_id,
        ];
    }

    /**
     * Override the method to include API token when requested via API
     */
    public function toArray()
    {
        $array = parent::toArray();
        
        // Only include API token when requested via API
        if (request()->wantsJson() || request()->is('api/*')) {
            $array['api_token'] = $this->getJWTIdentifier();
        }
        
        return $array;
    }
}