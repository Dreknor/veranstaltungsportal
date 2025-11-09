<?php

namespace Tests\Unit\Models;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Models\DiscountCode;
use App\Models\BookingItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_booking_number_on_creation()
    {
        $booking = Booking::factory()->create();

        $this->assertNotNull($booking->booking_number);
        $this->assertStringStartsWith('BK-', $booking->booking_number);
    }

    /** @test */
    public function it_belongs_to_an_event()
    {
        $event = Event::factory()->create();
        $booking = Booking::factory()->create(['event_id' => $event->id]);

        $this->assertInstanceOf(Event::class, $booking->event);
        $this->assertEquals($event->id, $booking->event->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $booking->user);
        $this->assertEquals($user->id, $booking->user->id);
    }

    /** @test */
    public function it_can_have_a_discount_code()
    {
        $discountCode = DiscountCode::factory()->create();
        $booking = Booking::factory()->create(['discount_code_id' => $discountCode->id]);

        $this->assertInstanceOf(DiscountCode::class, $booking->discountCode);
    }

    /** @test */
    public function it_has_many_items()
    {
        $booking = Booking::factory()->create();
        BookingItem::factory()->count(3)->create(['booking_id' => $booking->id]);

        $this->assertCount(3, $booking->items);
    }

    /** @test */
    public function it_has_total_amount_attribute()
    {
        $booking = Booking::factory()->create(['total' => 150.50]);

        $this->assertEquals(150.50, $booking->getTotalAmountAttribute());
    }

    /** @test */
    public function it_generates_verification_code()
    {
        $booking = Booking::factory()->create(['booking_number' => 'BK-ABC123DEF456']);

        $verificationCode = $booking->getVerificationCodeAttribute();

        $this->assertIsString($verificationCode);
        $this->assertEquals(8, strlen($verificationCode));
        $this->assertEquals(strtoupper($verificationCode), $verificationCode);
    }

    /** @test */
    public function it_scopes_confirmed_bookings()
    {
        Booking::factory()->count(3)->create(['status' => 'confirmed']);
        Booking::factory()->count(2)->create(['status' => 'pending']);

        $this->assertCount(3, Booking::confirmed()->get());
    }

    /** @test */
    public function it_scopes_pending_bookings()
    {
        Booking::factory()->count(2)->create(['status' => 'pending']);
        Booking::factory()->count(3)->create(['status' => 'confirmed']);

        $this->assertCount(2, Booking::pending()->get());
    }

    /** @test */
    public function it_casts_dates_correctly()
    {
        $booking = Booking::factory()->create([
            'email_verified_at' => now(),
            'confirmed_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $booking->email_verified_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $booking->confirmed_at);
    }

    /** @test */
    public function it_casts_decimal_fields_correctly()
    {
        $booking = Booking::factory()->create([
            'subtotal' => 100.50,
            'discount' => 10.25,
            'total' => 90.25,
        ]);

        $this->assertEquals('100.50', $booking->subtotal);
        $this->assertEquals('10.25', $booking->discount);
        $this->assertEquals('90.25', $booking->total);
    }
}

