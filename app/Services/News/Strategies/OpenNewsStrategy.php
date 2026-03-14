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

class OpenNewsStrategy implements NewsFetcherInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private CategoryMapperInterface $categoryMapper,
        private string $rssFeedUrl = '',
    ) {
        $this->rssFeedUrl = config('services.news_sources.opennews.rss_feed_url');
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

        try {
            $response = Http::timeout(10)->get($this->rssFeedUrl);

            $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);

            if (!$xml || !isset($xml->channel->item)) {
                return collect();
            }

            $articles = $xml->channel->item;

            return collect($articles)->map(function ($article) use ($category) {
                return new ArticleDTO(
                    title: (string) $article->title,
                    url: (string) $article->link,
                    source: $this->getSourceName(),
                    category: $category,
                    publishedAt: Carbon::parse((string) $article->pubDate),
                    description: isset($article->description) ? strip_tags((string) $article->description) : null,
                );
            });
        } catch (\Exception $e) {
            Log::error("{$this->getSourceName()} failed on " . implode(', ', $sourceCategories) . ": " . $e->getMessage());
            return collect();
        }
    }

    /**
     * @inheritdoc
     */
    public function getSourceName(): string
    {
        return 'OpenNews';
    }
}
