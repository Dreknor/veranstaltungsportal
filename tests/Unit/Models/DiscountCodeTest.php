<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\Test;
use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountCodeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_an_event()
    {
        $event = Event::factory()->create();
        $discountCode = DiscountCode::factory()->create(['event_id' => $event->id]);

        $this->assertInstanceOf(Event::class, $discountCode->event);
        $this->assertEquals($event->id, $discountCode->event->id);
    }

    #[Test]
    public function it_has_many_bookings()
    {
        $discountCode = DiscountCode::factory()->create();
        Booking::factory()->count(3)->create(['discount_code_id' => $discountCode->id]);

        $this->assertCount(3, $discountCode->bookings);
    }

    #[Test]
    public function it_is_valid_when_active_and_within_date_range()
    {
        $discountCode = DiscountCode::factory()->create([
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDay(),
            'usage_limit' => 10,
            'usage_count' => 5,
        ]);

        $this->assertTrue($discountCode->isValid());
    }

    #[Test]
    public function it_is_not_valid_when_inactive()
    {
        $discountCode = DiscountCode::factory()->create([
            'is_active' => false,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDay(),
        ]);

        $this->assertFalse($discountCode->isValid());
    }

    #[Test]
    public function it_is_not_valid_before_valid_from_date()
    {
        $discountCode = DiscountCode::factory()->create([
            'is_active' => true,
            'valid_from' => now()->addDay(),
            'valid_until' => now()->addWeek(),
        ]);

        $this->assertFalse($discountCode->isValid());
    }

    #[Test]
    public function it_is_not_valid_after_valid_until_date()
    {
        $discountCode = DiscountCode::factory()->create([
            'is_active' => true,
            'valid_from' => now()->subWeek(),
            'valid_until' => now()->subDay(),
        ]);

        $this->assertFalse($discountCode->isValid());
    }

    #[Test]
    public function it_is_not_valid_when_usage_limit_reached()
    {
        $discountCode = DiscountCode::factory()->create([
            'is_active' => true,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDay(),
            'usage_limit' => 10,
            'usage_count' => 10,
        ]);

        $this->assertFalse($discountCode->isValid());
    }

    #[Test]
    public function it_calculates_percentage_discount()
    {
        $discountCode = DiscountCode::factory()->create([
            'type' => 'percentage',
            'value' => 20,
        ]);

        $discount = $discountCode->calculateDiscount(100);

        $this->assertEquals(20, $discount);
    }

    #[Test]
    public function it_calculates_fixed_discount()
    {
        $discountCode = DiscountCode::factory()->create([
            'type' => 'fixed',
            'value' => 15,
        ]);

        $discount = $discountCode->calculateDiscount(100);

        $this->assertEquals(15, $discount);
    }

    #[Test]
    public function it_caps_fixed_discount_at_total_amount()
    {
        $discountCode = DiscountCode::factory()->create([
            'type' => 'fixed',
            'value' => 50,
        ]);

        $discount = $discountCode->calculateDiscount(30);

        $this->assertEquals(30, $discount);
    }
}




