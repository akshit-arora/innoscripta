<?php

namespace App\Jobs;

use App\Enums\ArticleCategory;
use App\Models\Article;
use App\Services\News\Contracts\NewsFetcherInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class FetchAndStoreNewsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $fetcherClass,
        public ArticleCategory $category
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fetcher = app($this->fetcherClass);

        if (! $fetcher instanceof NewsFetcherInterface) {
            throw new \Exception('Invalid fetcher class');
        }

        $articleDTOs = $fetcher->fetchArticles($this->category);

        if ($articleDTOs->isEmpty()) {
            return;
        }

        $insertArray = $articleDTOs->map(fn($dto) => $dto->toArray())->toArray();

        Article::upsert(
            $insertArray,
            ['url'],
            ['title', 'description', 'content', 'image_url', 'author', 'published_at', 'category', 'source', 'updated_at']
        );
    }
}
