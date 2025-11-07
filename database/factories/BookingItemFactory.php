<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingItemFactory extends Factory
{
    protected $model = BookingItem::class;

    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'ticket_type_id' => TicketType::factory(),
            'price' => $this->faker->randomFloat(2, 10, 150),
            'quantity' => 1,
            'checked_in' => false,
            'checked_in_at' => null,
        ];
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'checked_in' => true,
            'checked_in_at' => now(),
        ]);
    }
}

