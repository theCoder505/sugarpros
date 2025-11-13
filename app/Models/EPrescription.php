<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_by_provider_id',
        'appointment_uid',
        'patient_name',
        'patient_id',
        
        'medication',
        'daily_use',
        'diagnosis',
        'start_date',
        'end_date',
        'comments',
        'dispense_quantity',
        'unit_of_drugs',
        'days_supply',
        'provider_name',
        'dxscript_prescription_id',
        'status',
        'pharmacy_name',
        'pharmacy_ncpdp',
        'sent_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function provider()
    {
        return $this->belongsTo(Provider::class, 'note_by_provider_id', 'provider_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id', 'patient_id');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class, 'appointment_uid', 'appointment_uid');
    }

    // Status helpers
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isSent()
    {
        return $this->status === 'sent';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => '<span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800">Draft</span>',
            'sent' => '<span class="px-2 py-1 text-xs rounded-full bg-blue-200 text-blue-800">Sent</span>',
            'filled' => '<span class="px-2 py-1 text-xs rounded-full bg-green-200 text-green-800">Filled</span>',
            'cancelled' => '<span class="px-2 py-1 text-xs rounded-full bg-red-200 text-red-800">Cancelled</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-800">Unknown</span>',
        };
    }
}