<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'title' => fake()->sentence(3),
            'content' => json_encode([
                'sections' => [
                    ['title' => fake()->sentence(2), 'intro' => fake()->sentence(10), 'items' => []],
                ],
            ], JSON_UNESCAPED_UNICODE),
        ];
    }
}
