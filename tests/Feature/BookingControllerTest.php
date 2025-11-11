<?php

use App\Models\Booking;
use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\User;

test('user can create a booking', function () {
    $event = Event::factory()->published()->create();
    $ticketType = TicketType::factory()->for($event)->create([
        'price' => 50,
        'quantity' => 100,
    ]);

    $response = $this->post(route('bookings.store', $event), [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '1234567890',
        'tickets' => [
            [
                'ticket_type_id' => $ticketType->id,
                'quantity' => 2,
            ],
        ],
    ]);

    $response->assertRedirect();
    expect(Booking::count())->toBe(1);

    $booking = Booking::first();
    expect($booking->customer_name)->toBe('John Doe');
    expect($booking->total)->toBe(100.0);
    expect($booking->items)->toHaveCount(2);
});

test('booking applies discount code correctly', function () {
    $event = Event::factory()->published()->create();
    $ticketType = TicketType::factory()->for($event)->create([
        'price' => 100,
        'quantity' => 100,
    ]);

    $discountCode = DiscountCode::factory()->create([
        'code' => 'SAVE20',
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'event_id' => null,
    ]);

    $response = $this->post(route('bookings.store', $event), [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'tickets' => [
            [
                'ticket_type_id' => $ticketType->id,
                'quantity' => 1,
            ],
        ],
        'discount_code' => 'SAVE20',
    ]);

    $response->assertRedirect();

    $booking = Booking::first();
    expect($booking->subtotal)->toBe(100.0);
    expect($booking->discount)->toBe(20.0);
    expect($booking->total)->toBe(80.0);
});

test('booking cannot exceed ticket quantity', function () {
    $event = Event::factory()->published()->create();
    $ticketType = TicketType::factory()->for($event)->create([
        'quantity' => 5,
    ]);

    $response = $this->post(route('bookings.store', $event), [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'tickets' => [
            [
                'ticket_type_id' => $ticketType->id,
                'quantity' => 10,
            ],
        ],
    ]);

    $response->assertSessionHas('error');
    expect(Booking::count())->toBe(0);
});

test('booking can be viewed with booking number', function () {
    $booking = Booking::factory()->create();

    $response = $this->get(route('bookings.show', $booking->booking_number));

    $response->assertRedirect(route('bookings.verify', $booking->booking_number));
});

test('booking can be cancelled within allowed timeframe', function () {
    $event = Event::factory()->create([
        'start_date' => now()->addDays(3),
    ]);

    $booking = Booking::factory()->for($event)->create([
        'status' => 'confirmed',
    ]);

    $ticketType = TicketType::factory()->for($event)->create([
        'quantity_sold' => 2,
    ]);

    $booking->items()->create([
        'ticket_type_id' => $ticketType->id,
        'price' => 50,
        'quantity' => 1,
    ]);

    $response = $this->post(route('bookings.cancel', $booking->booking_number));

    $booking->refresh();
    expect($booking->status)->toBe('cancelled');
    expect($booking->cancelled_at)->not->toBeNull();
});

