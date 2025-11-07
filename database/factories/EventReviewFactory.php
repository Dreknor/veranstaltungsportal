<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventReview;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventReviewFactory extends Factory
{
    protected $model = EventReview::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional(0.7)->paragraph(),
        ];
    }

    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => 5,
            'comment' => $this->faker->paragraph(),
        ]);
    }

    public function poor(): static
    {
        return $this->state(fn (array $attributes) => [
            'rating' => $this->faker->numberBetween(1, 2),
            'comment' => $this->faker->paragraph(),
        ]);
    }
}

