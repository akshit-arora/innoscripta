<?php

namespace App\Models;

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'published_at' => 'datetime',
        'category' => ArticleCategory::class,
        'source' => NewsSource::class,
    ];
}
