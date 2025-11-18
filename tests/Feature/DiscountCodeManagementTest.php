<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\DiscountCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiscountCodeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_create_discount_code()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $discountData = [
            'code' => 'SAVE20',
            'type' => 'percentage',
            'value' => 20,
            'usage_limit' => 100,
            'valid_from' => now()->format('Y-m-d H:i:s'),
            'valid_until' => now()->addMonth()->format('Y-m-d H:i:s'),
            'is_active' => true,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.discount-codes.store', $event), $discountData);

        $this->assertDatabaseHas('discount_codes', [
            'event_id' => $event->id,
            'code' => 'SAVE20',
            'type' => 'percentage',
            'value' => 20,
        ]);
    }

    #[Test]
    public function organizer_can_create_fixed_discount_code()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $discountData = [
            'code' => 'FIXED10',
            'type' => 'fixed',
            'value' => 10,
            'is_active' => true,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.discount-codes.store', $event), $discountData);

        $this->assertDatabaseHas('discount_codes', [
            'event_id' => $event->id,
            'code' => 'FIXED10',
            'type' => 'fixed',
        ]);
    }

    #[Test]
    public function organizer_can_update_discount_code()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $discountCode = DiscountCode::factory()->create(['event_id' => $event->id]);

        $updateData = [
            'code' => $discountCode->code,
            'type' => 'percentage',
            'value' => 30,
            'is_active' => true,
        ];

        $response = $this->actingAs($organizer)
            ->put(route('organizer.events.discount-codes.update', [$event, $discountCode]), $updateData);

        $this->assertDatabaseHas('discount_codes', [
            'id' => $discountCode->id,
            'value' => 30,
        ]);
    }

    #[Test]
    public function organizer_can_deactivate_discount_code()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $discountCode = DiscountCode::factory()->create([
            'event_id' => $event->id,
            'is_active' => true,
        ]);

        $updateData = [
            'code' => $discountCode->code,
            'type' => $discountCode->type,
            'value' => $discountCode->value,
            'is_active' => false,
        ];

        $response = $this->actingAs($organizer)
            ->put(route('organizer.events.discount-codes.update', [$event, $discountCode]), $updateData);

        $this->assertDatabaseHas('discount_codes', [
            'id' => $discountCode->id,
            'is_active' => false,
        ]);
    }

    #[Test]
    public function organizer_can_delete_discount_code()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);
        $discountCode = DiscountCode::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer)
            ->delete(route('organizer.events.discount-codes.destroy', [$event, $discountCode]));

        $this->assertDatabaseMissing('discount_codes', [
            'id' => $discountCode->id,
        ]);
    }

    #[Test]
    public function discount_code_must_be_unique_per_event()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        DiscountCode::factory()->create([
            'event_id' => $event->id,
            'code' => 'DUPLICATE',
        ]);

        $discountData = [
            'code' => 'DUPLICATE',
            'type' => 'percentage',
            'value' => 20,
            'is_active' => true,
        ];

        $response = $this->actingAs($organizer)
            ->post(route('organizer.events.discount-codes.store', $event), $discountData);

        $response->assertSessionHasErrors('code');
    }

    #[Test]
    public function organizer_cannot_modify_discount_codes_of_other_events()
    {
        $organizer1 = User::factory()->create(['user_type' => 'organizer']);
        $organizer2 = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer2->id]);
        $discountCode = DiscountCode::factory()->create(['event_id' => $event->id]);

        $response = $this->actingAs($organizer1)
            ->delete(route('organizer.events.discount-codes.destroy', [$event, $discountCode]));

        $response->assertStatus(403);
    }
}



