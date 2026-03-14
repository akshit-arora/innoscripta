<?php

use App\Models\Article;
use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can list articles', function () {
    Article::factory()->count(5)->create();

    $response = $this->getJson('/api/articles');

    $response->assertStatus(200)
        ->assertJsonCount(5, 'data');
});

test('it can filter articles by search term', function () {
    Article::factory()->create(['title' => 'Specific Title']);
    Article::factory()->create(['title' => 'Other Title']);

    $response = $this->getJson('/api/articles?search=Specific');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Specific Title');
});

test('it can filter articles by category', function () {
    Article::factory()->create(['category' => ArticleCategory::TECHNOLOGY]);
    Article::factory()->create(['category' => ArticleCategory::SPORTS]);

    $response = $this->getJson('/api/articles?category=technology');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.category', 'technology');
});

test('it can filter articles by source', function () {
    Article::factory()->create(['source' => NewsSource::NEWS_API]);
    Article::factory()->create(['source' => NewsSource::NY_TIMES]);

    $response = $this->getJson('/api/articles?source=news_api');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.source', 'news_api');
});

test('it can filter articles by date', function () {
    $date = now()->format('Y-m-d');
    Article::factory()->create(['published_at' => $date]);
    Article::factory()->create(['published_at' => now()->subDays(2)]);

    $response = $this->getJson("/api/articles?date={$date}");

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

test('it can filter articles by user preferences', function () {
    Article::factory()->create(['source' => NewsSource::NEWS_API, 'category' => ArticleCategory::TECHNOLOGY]);
    Article::factory()->create(['source' => NewsSource::NY_TIMES, 'category' => ArticleCategory::SPORTS]);
    Article::factory()->create(['source' => NewsSource::OPEN_NEWS, 'category' => ArticleCategory::BUSINESS]);

    $response = $this->getJson('/api/articles?' . http_build_query([
        'preferences' => [
            'sources' => ['news_api', 'ny_times'],
            'categories' => ['technology', 'business']
        ]
    ]));

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});
