<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\FeaturedEventFee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeaturedEventManagementControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $organizer;
    private Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->organizer = User::factory()->create(['is_organizer' => true]);
        $this->organizer->assignRole('organizer');

        $this->event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'published' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_featured_events_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.featured-events.index'));

        $response->assertOk();
        $response->assertViewIs('admin.featured-events.index');
        $response->assertViewHas(['fees', 'stats']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_view_featured_events_index(): void
    {
        $user = User::factory()->create();
        // User has no admin role

        $response = $this->actingAs($user)->get(route('admin.featured-events.index'));

        $response->assertStatus(403); // Forbidden because AdminMiddleware blocks non-admins
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_filter_featured_events_by_status(): void
    {
        $pendingFee = FeaturedEventFee::factory()->create([
            'event_id' => $this->event->id,
            'payment_status' => 'pending',
        ]);

        $paidFee = FeaturedEventFee::factory()->create([
            'event_id' => Event::factory()->create(['organizer_id' => $this->organizer->id])->id,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.featured-events.index', ['status' => 'pending']));

        $response->assertOk();
        $response->assertSee($pendingFee->event->title);
        $response->assertDontSee($paidFee->event->title);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_featured_event_details(): void
    {
        $fee = FeaturedEventFee::factory()->create([
            'event_id' => $this->event->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.featured-events.show', $fee));

        $response->assertOk();
        $response->assertViewIs('admin.featured-events.show');
        $response->assertSee($fee->event->title);
        $response->assertSee(number_format($fee->amount, 2, ',', '.'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_payment_status(): void
    {
        $fee = FeaturedEventFee::factory()->create([
            'event_id' => $this->event->id,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.featured-events.update-status', $fee), [
                'payment_status' => 'paid',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('featured_event_fees', [
            'id' => $fee->id,
            'payment_status' => 'paid',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_cancel_pending_fee(): void
    {
        $fee = FeaturedEventFee::factory()->create([
            'event_id' => $this->event->id,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.featured-events.cancel', $fee));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('featured_event_fees', [
            'id' => $fee->id,
            'payment_status' => 'failed',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_cannot_cancel_paid_fee(): void
    {
        $fee = FeaturedEventFee::factory()->create([
            'event_id' => $this->event->id,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.featured-events.cancel', $fee));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('featured_event_fees', [
            'id' => $fee->id,
            'payment_status' => 'paid',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_extend_fee_period(): void
    {
        $fee = FeaturedEventFee::factory()->create([
            'event_id' => $this->event->id,
            'payment_status' => 'paid',
            'expires_at' => now()->addDays(7),
        ]);

        $originalExpiry = $fee->expires_at;

        $response = $this->actingAs($this->admin)
            ->post(route('admin.featured-events.extend', $fee), [
                'duration_type' => 'weekly',
                'duration_count' => 2,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $fee->refresh();
        $this->assertTrue($fee->expires_at->greaterThan($originalExpiry));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_statistics(): void
    {
        FeaturedEventFee::factory()->count(5)->create([
            'event_id' => Event::factory()->create(['organizer_id' => $this->organizer->id])->id,
            'payment_status' => 'paid',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.featured-events.statistics'));

        $response->assertOk();
        $response->assertViewIs('admin.featured-events.statistics');
        $response->assertViewHas(['revenueByDay', 'byDuration', 'topOrganizers']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_perform_bulk_actions(): void
    {
        $fees = FeaturedEventFee::factory()->count(3)->create([
            'event_id' => Event::factory()->create(['organizer_id' => $this->organizer->id])->id,
            'payment_status' => 'pending',
        ]);

        $feeIds = $fees->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.featured-events.bulk'), [
                'action' => 'mark_paid',
                'fee_ids' => $feeIds,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($feeIds as $feeId) {
            $this->assertDatabaseHas('featured_event_fees', [
                'id' => $feeId,
                'payment_status' => 'paid',
            ]);
        }
    }
}

