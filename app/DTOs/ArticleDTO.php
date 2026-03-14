<?php

namespace App\DTOs;

use App\Enums\ArticleCategory;
use Carbon\Carbon;

class ArticleDTO
{
    /**
     * Create a new DTO instance.
     */
    public function __construct(
        public string $title,
        public string $url,
        public string $source,
        public ArticleCategory $category,
        public Carbon $publishedAt,
        public ?string $description = null,
        public ?string $content = null,
        public ?string $imageUrl = null,
        public ?string $author = null,
    ) {}

    /**
     * Convert DTO to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'title' => substr($this->title, 0, 500),
            'description' => $this->description,
            'content' => $this->content,
            'image_url' => $this->imageUrl ? substr($this->imageUrl, 0, 500) : null,
            'author' => $this->author ? substr($this->author, 0, 255) : null,
            'url' => substr($this->url, 0, 500),
            'source' => $this->source,
            'category' => $this->category,
            'published_at' => $this->publishedAt->toDateTimeString(),
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ];
    }
}
