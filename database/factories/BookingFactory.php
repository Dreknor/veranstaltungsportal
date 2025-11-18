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
            'invoice_number' => null,
            'invoice_date' => null,
            'customer_name' => $this->faker->name(),
            'customer_email' => $this->faker->email(),
            'customer_phone' => $this->faker->optional()->phoneNumber(),
            'billing_address' => $this->faker->streetAddress(),
            'billing_postal_code' => $this->faker->postcode(),
            'billing_city' => $this->faker->city(),
            'billing_country' => 'Deutschland',
            'email_verification_token' => null,
            'email_verified_at' => null,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer', 'cash']),
            'discount_code_id' => null,
            'confirmed_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 month', 'now'),
            'cancelled_at' => null,
            'certificate_generated_at' => null,
            'certificate_path' => null,
            'additional_data' => null,
            'checked_in' => false,
            'checked_in_at' => null,
            'checked_in_by' => null,
            'check_in_method' => null,
            'check_in_notes' => null,
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

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'checked_in' => true,
            'checked_in_at' => now(),
        ]);
    }
}

