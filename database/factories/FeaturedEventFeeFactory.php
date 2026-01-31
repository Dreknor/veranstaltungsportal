<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\FeaturedEventFee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FeaturedEventFeeFactory extends Factory
{
    protected $model = FeaturedEventFee::class;

    public function definition(): array
    {
        $durationType = $this->faker->randomElement(['daily', 'weekly', 'monthly', 'custom']);
        $startDate = $this->faker->dateTimeBetween('now', '+1 month');

        $durationDays = match($durationType) {
            'daily' => 1,
            'weekly' => 7,
            'monthly' => 30,
            'custom' => $this->faker->numberBetween(1, 60),
        };

        $endDate = (clone $startDate)->modify("+{$durationDays} days");

        $feeAmount = match($durationType) {
            'daily' => 9.99,
            'weekly' => 49.99,
            'monthly' => 149.99,
            'custom' => $this->faker->randomFloat(2, 10, 200),
        };

        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'duration_type' => $durationType,
            'duration_days' => $durationType === 'custom' ? $durationDays : null,
            'featured_start_date' => $startDate,
            'featured_end_date' => $endDate,
            'fee_amount' => $feeAmount,
            'payment_status' => 'pending',
            'paid_at' => null,
            'payment_method' => null,
            'payment_reference' => null,
            'notes' => null,
            'expiry_notified_at' => null,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $this->faker->randomElement(['invoice', 'credit_card', 'paypal']),
            'payment_reference' => $this->faker->optional()->regexify('[A-Z0-9]{10}'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'pending',
            'paid_at' => null,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'failed',
            'paid_at' => null,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'refunded',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now(),
            'featured_start_date' => now()->subDays(2),
            'featured_end_date' => now()->addDays(5),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now()->subDays(10),
            'featured_start_date' => now()->subDays(8),
            'featured_end_date' => now()->subDays(1),
        ]);
    }
}
