<?php

namespace App\Services\News\Contracts;

use App\Enums\ArticleCategory;

interface CategoryMapperInterface
{
    /**
     * Map system categories to news source categories
     *
     * @param ArticleCategory $category
     * @return array<string>
     */
    public function map(ArticleCategory $category): array;
}
