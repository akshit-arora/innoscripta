<?php

namespace App\Services\News\Mappers;

use App\Enums\ArticleCategory;
use App\Services\News\Contracts\CategoryMapperInterface;

class NewsApiCategoryMapper implements CategoryMapperInterface
{
    /**
     * @inheritdoc
     */
    public function map(ArticleCategory $systemCategory): string
    {
        return match ($systemCategory) {
            ArticleCategory::BUSINESS => 'business',
            ArticleCategory::ENTERTAINMENT => 'entertainment',
            ArticleCategory::GENERAL => 'general',
            ArticleCategory::HEALTH => 'health',
            ArticleCategory::SCIENCE => 'science',
            ArticleCategory::SPORTS => 'sports',
            ArticleCategory::TECHNOLOGY => 'technology',
        };
    }
}
