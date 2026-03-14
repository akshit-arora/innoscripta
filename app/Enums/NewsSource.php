<?php

namespace App\Enums;

use App\Services\News\Strategies\NewsApiStrategy;
use App\Services\News\Strategies\OpenNewsStrategy;
use App\Services\News\Strategies\NYTimesStrategy;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "NewsSource",
    title: "News Source",
    description: "News source enum",
    type: "string",
    enum: ["news_api", "open_news", "ny_times"]
)]
enum NewsSource: string
{
    case NEWS_API = 'news_api';
    case OPEN_NEWS = 'open_news';
    case NY_TIMES = 'ny_times';

    /**
     * Get the strategy class for the news source.
     *
     * @return string
     */
    public function getStrategyClass(): string
    {
        return match ($this) {
            self::NEWS_API => NewsApiStrategy::class,
            self::OPEN_NEWS => OpenNewsStrategy::class,
            self::NY_TIMES => NYTimesStrategy::class,
        };
    }

    /**
     * Get the label for the news source.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NEWS_API => 'NewsAPI',
            self::OPEN_NEWS => 'OpenNews',
            self::NY_TIMES => 'NYTimes',
        };
    }
}
