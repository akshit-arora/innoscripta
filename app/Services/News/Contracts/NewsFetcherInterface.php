<?php

namespace App\Services\News\Contracts;

use App\Enums\ArticleCategory;
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
     * @return string
     */
    public function getSourceName(): string;
}
