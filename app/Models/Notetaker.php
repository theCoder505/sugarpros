<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notetaker extends Model
{
    use HasFactory;

    protected $table = 'notetakers';

    protected $fillable = [
        'note_uid',
        'provider_id',
        'appointment_id',
        'video_url',
    ];
}
