<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function organizer_can_view_event_statistics()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.statistics', $event));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_can_view_booking_report()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        Booking::factory()->count(10)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.reports.bookings', $event));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_can_export_attendee_list()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.export-attendees', $event));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_can_view_revenue_report()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'total' => 100,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.reports.revenue'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_platform_statistics()
    {
        $admin = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $admin->assignRole($adminRole);

        Event::factory()->count(20)->create(['is_published' => true]);
        Booking::factory()->count(50)->create(['status' => 'confirmed']);

        $response = $this->actingAs($admin)->get(route('admin.statistics'));

        $response->assertStatus(200);
    }

    /** @test */
    public function organizer_cannot_view_other_organizers_reports()
    {
        $organizer1 = User::factory()->create(['user_type' => 'organizer']);
        $organizer2 = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer2->id]);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.statistics', $event));

        $response->assertStatus(403);
    }
}


