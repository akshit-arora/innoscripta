<?php

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use App\Services\News\Mappers\NewsApiCategoryMapper;
use App\Services\News\Strategies\NewsApiStrategy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('services.news_sources.newsapi.base_url', 'https://newsapi.org/v2');
    Config::set('services.news_sources.newsapi.api_key', 'test-key');
});

test('it correctly identifies supported categories', function () {
    $strategy = new NewsApiStrategy(new NewsApiCategoryMapper());
    
    expect($strategy->supportsCategory(ArticleCategory::TECHNOLOGY))->toBeTrue();
    // Assuming OpenNews doesn't support Technology but NewsApi does
});

test('it fetches and maps articles correctly', function () {
    Http::fake([
        'newsapi.org/v2/top-headlines*' => Http::response([
            'status' => 'ok',
            'articles' => [
                [
                    'title' => 'Test Article',
                    'description' => 'Test Description',
                    'content' => 'Test Content',
                    'urlToImage' => 'https://example.com/image.jpg',
                    'author' => 'John Doe',
                    'url' => 'https://example.com/article',
                    'publishedAt' => '2024-03-14T21:55:00Z',
                ]
            ]
        ], 200),
    ]);

    $strategy = new NewsApiStrategy(new NewsApiCategoryMapper());
    $articles = $strategy->fetchArticles(ArticleCategory::TECHNOLOGY);

    expect($articles)->toHaveCount(1);
    $article = $articles->first();
    
    expect($article->title)->toBe('Test Article');
    expect($article->source)->toBe(NewsSource::NEWS_API);
    expect($article->category)->toBe(ArticleCategory::TECHNOLOGY);
});

test('it handles failed api responses gracefuly', function () {
    Http::fake([
        'newsapi.org/v2/top-headlines*' => Http::response(['message' => 'Error'], 500),
    ]);

    $strategy = new NewsApiStrategy(new NewsApiCategoryMapper());
    $articles = $strategy->fetchArticles(ArticleCategory::TECHNOLOGY);

    expect($articles)->toBeEmpty();
});
