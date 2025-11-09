<?php

namespace Database\Factories;

use App\Models\EventSeries;
use App\Models\User;
use App\Models\EventCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventSeriesFactory extends Factory
{
    protected $model = EventSeries::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'event_category_id' => EventCategory::factory(),
            'recurrence_type' => fake()->randomElement(['daily', 'weekly', 'monthly']),
            'recurrence_interval' => 1,
            'recurrence_days' => null,
            'recurrence_count' => fake()->numberBetween(5, 20),
            'recurrence_end_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'template_data' => [],
            'is_active' => true,
            'total_events' => 0,
        ];
    }
}
