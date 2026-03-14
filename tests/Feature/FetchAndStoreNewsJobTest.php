<?php

use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use App\Jobs\FetchAndStoreNewsJob;
use App\Models\Article;
use App\Services\News\Contracts\NewsFetcherInterface;
use App\DTOs\ArticleDTO;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

uses(RefreshDatabase::class);

test('it fetches and stores articles into the database', function () {
    $now = now();
    $dto = new ArticleDTO(
        title: 'Job Article',
        url: 'https://example.com/job',
        source: NewsSource::NEWS_API,
        category: ArticleCategory::TECHNOLOGY,
        publishedAt: $now,
    );

    $mockFetcher = Mockery::mock(NewsFetcherInterface::class);
    $mockFetcher->shouldReceive('fetchArticles')
        ->once()
        ->with(ArticleCategory::TECHNOLOGY)
        ->andReturn(collect([$dto]));

    // We need to bind the mock to the container
    app()->bind(NewsSource::NEWS_API->getStrategyClass(), fn() => $mockFetcher);

    $job = new FetchAndStoreNewsJob(NewsSource::NEWS_API, ArticleCategory::TECHNOLOGY);
    $job->handle();

    $this->assertDatabaseHas('articles', [
        'title' => 'Job Article',
        'url' => 'https://example.com/job',
        'source' => NewsSource::NEWS_API->value,
        'category' => ArticleCategory::TECHNOLOGY->value,
    ]);
});

test('it does not fail when no articles are fetched', function () {
    $mockFetcher = Mockery::mock(NewsFetcherInterface::class);
    $mockFetcher->shouldReceive('fetchArticles')
        ->once()
        ->andReturn(collect());

    app()->bind(NewsSource::NEWS_API->getStrategyClass(), fn() => $mockFetcher);

    $job = new FetchAndStoreNewsJob(NewsSource::NEWS_API, ArticleCategory::TECHNOLOGY);
    $job->handle();

    $this->assertEquals(0, Article::count());
});
