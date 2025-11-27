<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating event dates (for events with multiple dates)
 */
class EventDateFactory extends Factory
{
    protected $model = EventDate::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+3 months');
        $endDate = (clone $startDate)->modify('+2 hours');

        return [
            'event_id' => Event::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'venue_name' => null, // Will use event's venue by default
            'venue_address' => null,
            'venue_city' => null,
            'venue_postal_code' => null,
            'venue_country' => null,
            'venue_latitude' => null,
            'venue_longitude' => null,
            'notes' => $this->faker->optional(0.3)->sentence(),
            'is_cancelled' => false,
            'cancellation_reason' => null,
        ];
    }

    /**
     * Create with custom venue (override event's default venue)
     */
    public function withCustomVenue(): static
    {
        return $this->state(fn (array $attributes) => [
            'venue_name' => $this->faker->company() . ' Hall',
            'venue_address' => $this->faker->streetAddress(),
            'venue_city' => $this->faker->city(),
            'venue_postal_code' => $this->faker->postcode(),
            'venue_country' => 'Germany',
            'venue_latitude' => $this->faker->latitude(47, 55),
            'venue_longitude' => $this->faker->longitude(6, 15),
        ]);
    }

    /**
     * Create cancelled date
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cancelled' => true,
            'cancellation_reason' => $this->faker->sentence(),
        ]);
    }

    /**
     * Create upcoming date
     */
    public function upcoming(): static
    {
        $startDate = $this->faker->dateTimeBetween('tomorrow', '+1 month');
        $endDate = (clone $startDate)->modify('+2 hours');

        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    /**
     * Create past date
     */
    public function past(): static
    {
        $startDate = $this->faker->dateTimeBetween('-3 months', '-1 week');
        $endDate = (clone $startDate)->modify('+2 hours');

        return $this->state(fn (array $attributes) => [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }
}

