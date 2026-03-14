<?php

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use App\Services\News\Mappers\NYTimesCategoryMapper;
use App\Services\News\Strategies\NYTimesStrategy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('services.news_sources.nytimes.base_url', 'https://api.nytimes.com/svc');
    Config::set('services.news_sources.nytimes.api_key', 'test-key');
});

test('it correctly identifies supported categories', function () {
    $strategy = new NYTimesStrategy(new NYTimesCategoryMapper());
    
    expect($strategy->supportsCategory(ArticleCategory::TECHNOLOGY))->toBeTrue();
    expect($strategy->supportsCategory(ArticleCategory::GENERAL))->toBeTrue();
});

test('it fetches and maps articles correctly from NYTimes', function () {
    Http::fake([
        'api.nytimes.com/svc/topstories/v2/technology.json*' => Http::response([
            'status' => 'OK',
            'results' => [
                [
                    'title' => 'NYT Article',
                    'abstract' => 'NYT abstract',
                    'url' => 'https://nytimes.com/article',
                    'byline' => 'By NYT Reporter',
                    'published_date' => '2024-03-14T21:55:00Z',
                    'multimedia' => [
                        ['url' => 'https://nytimes.com/image.jpg']
                    ]
                ]
            ]
        ], 200),
    ]);

    $strategy = new NYTimesStrategy(new NYTimesCategoryMapper());
    $articles = $strategy->fetchArticles(ArticleCategory::TECHNOLOGY);

    expect($articles)->toHaveCount(1);
    $article = $articles->first();
    
    expect($article->title)->toBe('NYT Article');
    expect($article->source)->toBe(NewsSource::NY_TIMES);
    expect($article->imageUrl)->toBe('https://nytimes.com/image.jpg');
});
