<?php

namespace App\Models;

use App\Enums\Category;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'image_url',
        'author',
        'published_at',
        'url',
        'category',
        'source',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'category' => Category::class,
    ];
}
