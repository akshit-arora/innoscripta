<?php

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use App\Services\News\Mappers\OpenNewsCategoryMapper;
use App\Services\News\Strategies\OpenNewsStrategy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('services.news_sources.opennews.rss_feed_url', 'https://example.com/rss');
});

test('it correctly identifies supported categories', function () {
    $strategy = new OpenNewsStrategy(new OpenNewsCategoryMapper());
    
    expect($strategy->supportsCategory(ArticleCategory::GENERAL))->toBeTrue();
    expect($strategy->supportsCategory(ArticleCategory::TECHNOLOGY))->toBeFalse();
});

test('it fetches and maps articles correctly from OpenNews RSS', function () {
    $rssContent = '<?xml version="1.0" encoding="UTF-8"?>
    <rss version="2.0">
        <channel>
            <item>
                <title>RSS Article</title>
                <link>https://example.com/rss/article</link>
                <pubDate>Thu, 14 Mar 2024 21:55:00 +0000</pubDate>
                <description><![CDATA[RSS description]]></description>
            </item>
        </channel>
    </rss>';

    Http::fake([
        'example.com/rss*' => Http::response($rssContent, 200),
    ]);

    $strategy = new OpenNewsStrategy(new OpenNewsCategoryMapper());
    $articles = $strategy->fetchArticles(ArticleCategory::GENERAL);

    expect($articles)->toHaveCount(1);
    $article = $articles->first();
    
    expect($article->title)->toBe('RSS Article');
    expect($article->source)->toBe(NewsSource::OPEN_NEWS);
    expect($article->description)->toBe('RSS description');
});
