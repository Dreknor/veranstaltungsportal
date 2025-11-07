<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 20, 500);
        $discount = $this->faker->optional(0.3)->randomFloat(2, 0, $subtotal * 0.3) ?? 0;
        $total = $subtotal - $discount;

        return [
            'event_id' => Event::factory(),
            'user_id' => User::factory(),
            'booking_number' => 'BK-' . strtoupper(Str::random(10)),
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'customer_phone' => $this->faker->optional()->phoneNumber(),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'refunded', 'failed']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash']),
            'discount_code_id' => null,
            'confirmed_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 month', 'now'),
            'cancelled_at' => null,
            'additional_data' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'confirmed_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
    }
}

