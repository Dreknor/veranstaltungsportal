<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_view_event_statistics()
    {
        $this->markTestSkipped('Route organizer.events.statistics is not implemented yet');

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.statistics', $event));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_can_view_booking_report()
    {
        $this->markTestSkipped('Route organizer.reports.bookings is not implemented yet');

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        Booking::factory()->count(10)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.reports.bookings', $event));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_can_export_attendee_list()
    {
        $this->markTestSkipped('Route organizer.events.export-attendees is not implemented yet');

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.events.export-attendees', $event));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_can_view_revenue_report()
    {
        $this->markTestSkipped('Route organizer.reports.revenue is not implemented yet');

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        Booking::factory()->count(5)->create([
            'event_id' => $event->id,
            'status' => 'confirmed',
            'total' => 100,
        ]);

        $response = $this->actingAs($organizer)->get(route('organizer.reports.revenue'));

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_can_view_platform_statistics()
    {
        $this->markTestSkipped('Route admin.statistics is not implemented yet');

        $admin = User::factory()->create();
        // Use firstOrCreate to avoid duplicate role error
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);

        Event::factory()->count(20)->create(['is_published' => true]);
        Booking::factory()->count(50)->create(['status' => 'confirmed']);

        $response = $this->actingAs($admin)->get(route('admin.statistics'));

        $response->assertStatus(200);
    }

    #[Test]
    public function organizer_cannot_view_other_organizers_reports()
    {
        $this->markTestSkipped('Route organizer.events.statistics is not implemented yet');

        $organizer1 = User::factory()->create();
        $organizer1->assignRole('organizer');
        $result1 = $this->createOrganizerWithOrganization($organizer1);

        $organizer2 = User::factory()->create();
        $organizer2->assignRole('organizer');
        $result2 = $this->createOrganizerWithOrganization($organizer2);

        $event = Event::factory()->create(['organization_id' => $result2['organization']->id]);

        $response = $this->actingAs($organizer1)->get(route('organizer.events.statistics', $event));

        $response->assertStatus(403);
    }
}




