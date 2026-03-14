<?php

use App\Enums\ArticleCategory;
use App\Services\News\Mappers\NYTimesCategoryMapper;
use App\Services\News\Mappers\NewsApiCategoryMapper;
use App\Services\News\Mappers\OpenNewsCategoryMapper;

test('NewsApiCategoryMapper maps categories correctly', function () {
    $mapper = new NewsApiCategoryMapper();
    
    expect($mapper->map(ArticleCategory::TECHNOLOGY))->toBe(['technology']);
    expect($mapper->map(ArticleCategory::BUSINESS))->toBe(['business']);
});

test('NYTimesCategoryMapper maps categories correctly', function () {
    $mapper = new NYTimesCategoryMapper();
    
    expect($mapper->map(ArticleCategory::TECHNOLOGY))->toContain('technology', 'automobiles');
    expect($mapper->map(ArticleCategory::GENERAL))->toContain('world', 'politics');
});

test('OpenNewsCategoryMapper maps categories correctly', function () {
    $mapper = new OpenNewsCategoryMapper();
    
    expect($mapper->map(ArticleCategory::GENERAL))->toBe(['general']);
    expect($mapper->map(ArticleCategory::TECHNOLOGY))->toBe([]);
});
