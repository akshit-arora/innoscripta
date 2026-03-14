<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content' => $this->faker->text,
            'image_url' => $this->faker->imageUrl,
            'author' => $this->faker->name,
            'published_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'url' => $this->faker->url,
            'category' => $this->faker->randomElement(\App\Enums\ArticleCategory::cases()),
            'source' => $this->faker->randomElement(\App\Enums\NewsSource::cases()),
        ];
    }
}
