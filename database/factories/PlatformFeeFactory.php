<?php

namespace Database\Factories;

use App\Models\PlatformFee;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlatformFee>
 */
class PlatformFeeFactory extends Factory
{
    protected $model = PlatformFee::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'booking_id' => Booking::factory(),
            'invoice_number' => null,
            'invoice_date' => null,
            'fee_percentage' => 5.00,
            'booking_amount' => $this->faker->randomFloat(2, 100, 1000),
            'fee_amount' => $this->faker->randomFloat(2, 1, 50),
            'status' => 'pending',
            'paid_at' => null,
        ];
    }

    /**
     * Indicate that the platform fee is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    /**
     * Indicate that the platform fee is refunded.
     */
    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'refunded',
            'paid_at' => null,
        ]);
    }
}

