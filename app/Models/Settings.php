<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;


    protected $fillable = [
        'brandname',
        'brandlogo',
        'brandicon',
        'currency',
        'contact_email',
        'stripe_client_id',
        'stripe_secret_key',
        'subscription_key',
        'OPENAI_API_KEY',
        'streets',
        'cities',
        'states',
        'zip_codes',
        'prefixcode',
        'languages',
        'DEXCOM_CLIENT_ID',
        'DEXCOM_CLIENT_SECRET',
        'DEXCOM_REDIRECT_URI',
        'meeting_web_root_url',
        'CLAIM_MD_CLIENT_ID',
        'CLAIM_MD_API_KEY',
        'CLAIM_MD_ENV',
        'FATSECRET_KEY',
        'FATSECRET_SECRET',
        'contact_phone',
        'fb_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',



        'stripe_amount',
        'medicare_amount',
        'monthly_basic_amount',
        'monthly_basic_price_key',
        'monthly_premium_amount',
        'monthly_premium_price_key',
        'monthly_vip_amount',
        'monthly_vip_price_key',
        'annual_basic_amount',
        'annual_basic_price_key',
        'annual_premium_amount',
        'annual_premium_price_key',
        'annual_vip_amount',
        'annual_vip_price_key',
    ];
}
