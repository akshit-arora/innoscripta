<?php

namespace App\Models;

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Scope for filtering articles.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query) use ($filters) {
            $query->where('title', 'like', "%{$filters['search']}%")
                ->orWhere('description', 'like', "%{$filters['search']}%")
                ->orWhere('content', 'like', "%{$filters['search']}%");
        });

        $query->when($filters['date'] ?? null, function ($query) use ($filters) {
            $query->whereDate('published_at', $filters['date']);
        });

        $query->when($filters['category'] ?? null, function ($query) use ($filters) {
            $query->where('category', $filters['category']);
        });

        $query->when($filters['source'] ?? null, function ($query) use ($filters) {
            $query->where('source', $filters['source']);
        });
    }

    /**
     * Scope for filtering articles based on user preferences.
     *
     * @param Builder $query
     * @param array $preferences
     * @return void
     */
    public function scopeWithPreferences(Builder $query, array $preferences): void
    {
        $query->when($preferences['sources'] ?? null, function ($query) use ($preferences) {
            $query->whereIn('source', $preferences['sources']);
        });

        $query->when($preferences['categories'] ?? null, function ($query) use ($preferences) {
            $query->whereIn('category', $preferences['categories']);
        });

        $query->when($preferences['authors'] ?? null, function ($query) use ($preferences) {
            $query->whereIn('author', $preferences['authors']);
        });
    }
}
