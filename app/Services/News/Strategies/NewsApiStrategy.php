<?php

namespace App\Services\News\Strategies;

use App\DTOs\ArticleDTO;
use App\Enums\ArticleCategory;
use App\Services\News\Contracts\CategoryMapperInterface;
use App\Services\News\Contracts\NewsFetcherInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiStrategy implements NewsFetcherInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CategoryMapperInterface $categoryMapper,
        private string $baseUrl = '',
        private string $apiKey = '',
    ) {
        $this->baseUrl = config('services.news_sources.newsapi.base_url');
        $this->apiKey = config('services.news_sources.newsapi.api_key');
    }

    /**
     * @inheritdoc
     */
    public function fetchArticles(ArticleCategory $category): Collection
    {
        $newsApiCategory = $this->categoryMapper->map($category);

        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/top-headlines', [
                'country' => 'us',
                'category' => $newsApiCategory,
                'apiKey' => $this->apiKey,
            ]);

            $articles = $response->json()['articles'];

            return collect($articles)->map(function ($article) use ($category) {
                return new ArticleDTO(
                    title: $article['title'],
                    description: $article['description'],
                    content: $article['content'],
                    imageUrl: $article['urlToImage'],
                    author: $article['author'],
                    url: $article['url'],
                    source: $this->getSourceName(),
                    category: $category,
                    publishedAt: Carbon::parse($article['publishedAt']),
                );
            });
        } catch (\Exception $e) {
            Log::error('NewsAPI fetch failed: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * @inheritdoc
     */
    public function getSourceName(): string
    {
        return 'NewsAPI';
    }
}
