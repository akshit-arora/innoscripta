<?php

namespace App\Providers;

use App\Services\News\Contracts\CategoryMapperInterface;
use App\Services\News\Mappers\NewsApiCategoryMapper;
use App\Services\News\Mappers\NYTimesCategoryMapper;
use App\Services\News\Mappers\OpenNewsCategoryMapper;
use App\Services\News\Strategies\NewsApiStrategy;
use App\Services\News\Strategies\NYTimesStrategy;
use App\Services\News\Strategies\OpenNewsStrategy;
use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->when(NewsApiStrategy::class)
            ->needs(CategoryMapperInterface::class)
            ->give(NewsApiCategoryMapper::class);

        $this->app->when(OpenNewsStrategy::class)
            ->needs(CategoryMapperInterface::class)
            ->give(OpenNewsCategoryMapper::class);

        $this->app->when(NYTimesStrategy::class)
            ->needs(CategoryMapperInterface::class)
            ->give(NYTimesCategoryMapper::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
