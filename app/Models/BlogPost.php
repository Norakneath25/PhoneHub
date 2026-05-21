<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'slug',
        'category',
        'title',
        'excerpt',
        'content',
        'author',
        'read_time',
        'image',
        'featured',
        'tags',
    ];

    protected $casts = [
        'tags'     => 'array',
        'featured' => 'boolean',
    ];
}