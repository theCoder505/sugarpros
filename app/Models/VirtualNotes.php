<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualNotes extends Model
{
    use HasFactory;

    protected $table = 'virtual_notes';

    protected $fillable = [
        'note_by_provider_id',
        'appointment_uid',
        'patient_id',
        'main_note',
    ];
}
