<?php

namespace App\Models;

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Article",
    title: "Article",
    description: "Article model",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Example Title"),
        new OA\Property(property: "description", type: "string", example: "Example Description"),
        new OA\Property(property: "content", type: "string", example: "Example Content"),
        new OA\Property(property: "image_url", type: "string", example: "https://example.com/image.jpg"),
        new OA\Property(property: "author", type: "string", example: "John Doe"),
        new OA\Property(property: "url", type: "string", example: "https://example.com/article"),
        new OA\Property(property: "category", type: "string", example: "technology"),
        new OA\Property(property: "source", type: "string", example: "news_api"),
        new OA\Property(property: "published_at", type: "string", format: "date-time", example: "2024-03-14T21:55:00Z"),
    ]
)]
class Article extends Model
{
    use HasFactory;

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
