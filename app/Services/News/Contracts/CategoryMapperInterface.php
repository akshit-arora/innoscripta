<?php

namespace App\Services\News\Contracts;

use App\Enums\ArticleCategory;

interface CategoryMapperInterface
{
    /**
     * Map system categories to news source categories
     *
     * @param ArticleCategory $category
     * @return string|null
     */
    public function map(ArticleCategory $category): ?string;
}
