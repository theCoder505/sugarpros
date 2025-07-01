<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'thumbnail',
        'short_details',
        'time_to_read',
        'table_of_contents',
        'content_images',
        'content_details',
    ];
}
