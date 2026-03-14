<?php

namespace App\Services\News\Strategies;

use App\DTOs\ArticleDTO;
use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use App\Services\News\Contracts\CategoryMapperInterface;
use App\Services\News\Contracts\NewsFetcherInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NYTimesStrategy implements NewsFetcherInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CategoryMapperInterface $categoryMapper,
        private string $baseUrl = '',
        private string $apiKey = '',
    ) {
        $this->baseUrl = config('services.news_sources.nytimes.base_url') ?? '';
        $this->apiKey = config('services.news_sources.nytimes.api_key') ?? '';
    }

    /**
     * @inheritdoc
     */
    public function supportsCategory(ArticleCategory $category): bool
    {
        return !empty($this->categoryMapper->map($category));
    }

    /**
     * @inheritdoc
     */
    public function fetchArticles(ArticleCategory $category): Collection
    {
        $sourceCategories = $this->categoryMapper->map($category);

        if (empty($sourceCategories)) {
            return collect();
        }

        $allArticles = collect();

        foreach ($sourceCategories as $sourceCategory) {
            try {
                // Top Stories API: /topstories/v2/{section}.json
                $response = Http::timeout(10)->get("{$this->baseUrl}/topstories/v2/{$sourceCategory}.json", [
                    'api-key' => $this->apiKey,
                ]);

                if (!$response->successful()) {
                    Log::error("NYTimes fetch failed for category {$sourceCategory}: " . $response->body());
                    continue;
                }

                $results = $response->json()['results'] ?? [];

                $mappedArticles = collect($results)->map(function ($article) use ($category) {
                    $imageUrl = null;
                    if (!empty($article['multimedia'])) {
                        // Find the first image or a specific format if preferred
                        $imageUrl = $article['multimedia'][0]['url'] ?? null;
                    }

                    return new ArticleDTO(
                        title: $article['title'],
                        description: $article['abstract'],
                        content: $article['abstract'],
                        imageUrl: $imageUrl,
                        author: $article['byline'],
                        url: $article['url'],
                        source: $this->getSource(),
                        category: $category,
                        publishedAt: Carbon::parse($article['published_date']),
                    );
                });

                $allArticles = $allArticles->concat($mappedArticles);
            } catch (\Exception $e) {
                Log::error("{$this->getSource()->label()} fetch exception for category {$sourceCategory}: " . $e->getMessage());
            }
        }

        return $allArticles;
    }

    /**
     * @inheritdoc
     */
    public function getSource(): NewsSource
    {
        return NewsSource::NY_TIMES;
    }
}
