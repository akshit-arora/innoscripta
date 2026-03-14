<?php

namespace App\Services\News\Mappers;

use App\Enums\ArticleCategory;
use App\Services\News\Contracts\CategoryMapperInterface;

class OpenNewsCategoryMapper implements CategoryMapperInterface
{
    /**
     * @inheritdoc
     */
    public function map(ArticleCategory $systemCategory): array
    {
        return match ($systemCategory) {
            ArticleCategory::GENERAL => ['general'],
            default => [],
        };
    }
}
