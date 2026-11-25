<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = ucfirst(fake()->unique()->words(3, true));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => fake()->sentence(),
            'description' => fake()->paragraphs(2, true),
            'starts_at' => $this->dateInDays(fake()->numberBetween(1, 56)),
            'price' => fake()->randomElement([0, 0, 5, 10, 15, 20]),
            'is_published' => false,
            'category_id' => Category::factory(),
            'venue_id' => Venue::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => $this->dateInDays(fake()->numberBetween(1, 56)),
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => $this->dateInDays(-fake()->numberBetween(1, 60)),
        ]);
    }

    private function dateInDays(int $days): Carbon
    {
        return today()->addDays($days)->setTime(fake()->numberBetween(9, 21), fake()->randomElement([0, 30]));
    }
}
