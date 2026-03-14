<?php

namespace App\Services\News\Mappers;

use App\Enums\ArticleCategory;
use App\Services\News\Contracts\CategoryMapperInterface;

class NYTimesCategoryMapper implements CategoryMapperInterface
{
    /**
     * @inheritdoc
     */
    public function map(ArticleCategory $systemCategory): array
    {
        return match ($systemCategory) {
            ArticleCategory::BUSINESS => ['business', 'realestate'],
            ArticleCategory::ENTERTAINMENT => ['arts', 'movies', 'theater', 'magazine', 't-magazine', 'books/review', 'fashion'],
            ArticleCategory::HEALTH => ['health'],
            ArticleCategory::SCIENCE => ['science'],
            ArticleCategory::SPORTS => ['sports'],
            ArticleCategory::TECHNOLOGY => ['technology', 'automobiles'],
            ArticleCategory::GENERAL => ['world', 'us', 'politics', 'nyregion', 'opinion', 'food', 'travel'],
            default => [],
        };
    }
}
