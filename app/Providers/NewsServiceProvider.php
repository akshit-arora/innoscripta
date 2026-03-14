<?php

namespace App\Providers;

use App\Services\News\Contracts\CategoryMapperInterface;
use App\Services\News\Mappers\NewsApiCategoryMapper;
use App\Services\News\Strategies\NewsApiStrategy;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
