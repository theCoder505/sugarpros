<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAggreemrnt extends Model
{
    use HasFactory;

    protected $table = 'financial_aggreemrnts';

    protected $fillable = [
        'user_id',
        'user_name',
        'patients_name',
        'patients_signature_date',
        'relationship',
    ];

}
