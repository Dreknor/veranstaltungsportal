<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    protected $model = TicketType::class;

    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 10, 150);
        $quantity = $this->faker->numberBetween(50, 500);

        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->randomElement(['VIP', 'Standard', 'Early Bird', 'Ermäßigt', 'Stehplatz', 'Sitzplatz']),
            'description' => $this->faker->optional()->sentence(),
            'price' => $price,
            'quantity' => $quantity,
            'quantity_sold' => 0,
            'sale_start' => $this->faker->optional(0.5)->dateTimeBetween('-1 month', 'now'),
            'sale_end' => $this->faker->optional(0.5)->dateTimeBetween('now', '+2 months'),
            'min_per_order' => 1,
            'max_per_order' => $this->faker->numberBetween(4, 10),
            'is_available' => true,
        ];
    }

    public function soldOut(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity_sold' => $attributes['quantity'],
            ];
        });
    }
}

