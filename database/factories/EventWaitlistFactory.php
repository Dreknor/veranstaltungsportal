<?php

namespace Database\Factories;

use App\Models\EventWaitlist;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventWaitlistFactory extends Factory
{
    protected $model = EventWaitlist::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'email' => fake()->safeEmail(),
            'name' => fake()->name(),
            'quantity' => fake()->numberBetween(1, 5),
            'notified_at' => null,
        ];
    }

    public function notified(): static
    {
        return $this->state(fn (array $attributes) => [
            'notified_at' => now(),
        ]);
    }
}

