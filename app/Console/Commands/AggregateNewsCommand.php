<?php

namespace App\Console\Commands;

use App\Enums\ArticleCategory;
use App\Jobs\FetchAndStoreNewsJob;
use App\Services\News\Strategies\NewsApiStrategy;
use App\Services\News\Strategies\OpenNewsStrategy;
use Illuminate\Console\Command;

class AggregateNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:aggregate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to fetch and store news from various sources';

    protected array $sources = [
        NewsApiStrategy::class,
        OpenNewsStrategy::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting news aggregation...');

        $delayCounter = 0;

        foreach ($this->sources as $sourceClass) {
            $fetcher = app($sourceClass);

            foreach (ArticleCategory::cases() as $category) {
                if (!$fetcher->supportsCategory($category)) {
                    $this->line('Skipping: ' . $sourceClass . ' for category ' . $category->value);
                    continue;
                }

                $delay = now()->addSeconds($delayCounter * 5);
                $this->line('Queued: ' . $sourceClass . ' for category ' . $category->value . ' with delay ' . $delay->diffForHumans());

                FetchAndStoreNewsJob::dispatch($sourceClass, $category)->delay($delay);

                $delayCounter++;
            }
        }

        $this->info('News aggregation completed.');

        return self::SUCCESS;
    }
}
