<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelPaymentForm extends Model
{
    use HasFactory;

    protected $table = 'sel_payment_forms';

    protected $fillable = [
        'user_id',
        'user_name',
        'patients_name',
        'patients_signature_date',
    ];
}
