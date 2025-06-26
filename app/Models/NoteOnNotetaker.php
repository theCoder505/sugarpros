<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteOnNotetaker extends Model
{
    use HasFactory;

    protected $table = 'note_on_notetakers';

    protected $fillable = [
        'note_uid',
        'provider_id',
        'note_text',
    ];
}
