<?php

namespace Database\Factories;

use App\Models\DiscountCode;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DiscountCodeFactory extends Factory
{
    protected $model = DiscountCode::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['percentage', 'fixed']);
        $value = $type === 'percentage'
            ? $this->faker->numberBetween(5, 50)
            : $this->faker->randomFloat(2, 5, 50);

        return [
            'event_id' => $this->faker->optional(0.5)->randomElement([null, Event::factory()]),
            'code' => strtoupper(Str::random(8)),
            'type' => $type,
            'value' => $value,
            'usage_limit' => $this->faker->optional(0.6)->numberBetween(10, 100),
            'usage_count' => 0,
            'valid_from' => $this->faker->optional(0.5)->dateTimeBetween('-1 month', 'now'),
            'valid_until' => $this->faker->optional(0.7)->dateTimeBetween('now', '+3 months'),
            'is_active' => true,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_until' => $this->faker->dateTimeBetween('-1 month', '-1 day'),
        ]);
    }

    public function maxedOut(): static
    {
        return $this->state(function (array $attributes) {
            $maxUses = $attributes['max_uses'] ?? 100;
            return [
                'max_uses' => $maxUses,
                'usage_count' => $maxUses,
            ];
        });
    }
}

