<?php

namespace App\Jobs;

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
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
        public NewsSource $source,
        public ArticleCategory $category
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fetcherClass = $this->source->getStrategyClass();
        $fetcher = app($fetcherClass);

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
