<?php

namespace App\Services\News\Contracts;

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use Illuminate\Support\Collection;

interface NewsFetcherInterface
{
    /**
     * Fetch articles from the news source.
     *
     * @param ArticleCategory $category
     * @return Collection
     */
    public function fetchArticles(ArticleCategory $category): Collection;

    /**
     * Get the name of the news source.
     *
     * @return NewsSource
     */
    public function getSource(): NewsSource;

    /**
     * Check if the news source supports the given category.
     *
     * @param ArticleCategory $category
     * @return bool
     */
    public function supportsCategory(ArticleCategory $category): bool;
}
