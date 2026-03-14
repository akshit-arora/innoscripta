<?php

use App\DTOs\ArticleDTO;
use App\Enums\ArticleCategory;
use App\Enums\NewsSource;
use Carbon\Carbon;

test('it can convert DTO to array with correct values', function () {
    $now = now();
    $dto = new ArticleDTO(
        title: 'Test Title',
        url: 'https://example.com',
        source: NewsSource::NEWS_API,
        category: ArticleCategory::TECHNOLOGY,
        publishedAt: $now,
        description: 'Test Description',
        content: 'Test Content',
        imageUrl: 'https://example.com/image.jpg',
        author: 'John Doe'
    );

    $array = $dto->toArray();

    expect($array['title'])->toBe('Test Title');
    expect($array['url'])->toBe('https://example.com');
    expect($array['source'])->toBe(NewsSource::NEWS_API);
    expect($array['category'])->toBe(ArticleCategory::TECHNOLOGY);
    expect($array['published_at'])->toBe($now->toDateTimeString());
    expect($array['description'])->toBe('Test Description');
    expect($array['content'])->toBe('Test Content');
    expect($array['image_url'])->toBe('https://example.com/image.jpg');
    expect($array['author'])->toBe('John Doe');
});

test('it truncates long strings to database limits', function () {
    $longTitle = str_repeat('a', 600);
    $longUrl = str_repeat('b', 600);
    $longImageUrl = str_repeat('c', 600);
    $longAuthor = str_repeat('d', 300);

    $dto = new ArticleDTO(
        title: $longTitle,
        url: $longUrl,
        source: NewsSource::NEWS_API,
        category: ArticleCategory::TECHNOLOGY,
        publishedAt: now(),
        imageUrl: $longImageUrl,
        author: $longAuthor
    );

    $array = $dto->toArray();

    expect(strlen($array['title']))->toBe(500);
    expect(strlen($array['url']))->toBe(500);
    expect(strlen($array['image_url']))->toBe(500);
    expect(strlen($array['author']))->toBe(255);
});
