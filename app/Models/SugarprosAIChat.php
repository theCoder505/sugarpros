<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SugarprosAIChat extends Model
{
    use HasFactory;

    protected $table = 'sugarpros_ai_chats';

    protected $fillable = [
        'requested_by',
        'requested_to',
        'chatuid',
        'message_of_uid',
        'message',
    ];



    public function firstMessage()
    {
        return $this->hasOne(SugarprosAIChat::class, 'chatuid', 'chatuid')->oldest('created_at');
    }
}
