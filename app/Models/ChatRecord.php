<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRecord extends Model
{
    use HasFactory;
    protected $table = 'chat_records';
    protected $fillable = [
        'sent_by',
        'sender_type',
        'received_by',
        'receiver_type',
        'status',
        'main_message',
    ];
}
