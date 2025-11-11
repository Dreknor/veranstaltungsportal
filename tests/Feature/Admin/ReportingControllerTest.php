<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\EventCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $organizer;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->organizer = User::factory()->create(['is_organizer' => true]);
        $this->user = User::factory()->create();
    }

    public function test_reporting_index_requires_authentication(): void
    {
        $response = $this->get(route('admin.reporting.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_reporting_index_requires_admin(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.reporting.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_reporting_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reporting.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reporting.index');
        $response->assertViewHas(['metrics', 'userGrowth', 'revenueData', 'categoryPerformance']);
    }

    public function test_reporting_shows_correct_metrics(): void
    {
        // Create test data
        User::factory()->count(5)->create();
        $category = EventCategory::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $category->id,
        ]);
        Booking::factory()->count(3)->create([
            'event_id' => $event->id,
            'payment_status' => 'paid',
            'total_amount' => 100,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reporting.index'));

        $response->assertStatus(200);
        $metrics = $response->viewData('metrics');

        $this->assertGreaterThanOrEqual(6, $metrics['total_users']); // 5 + admin
        $this->assertEquals(1, $metrics['total_events']);
        $this->assertEquals(3, $metrics['total_bookings']);
        $this->assertEquals(300, $metrics['total_revenue']);
    }

    public function test_reporting_filters_by_period(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reporting.index', ['period' => '7days']));

        $response->assertStatus(200);
        $response->assertViewHas('period', '7days');
    }

    public function test_users_report_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reporting.users'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reporting.users');
    }

    public function test_events_report_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reporting.events'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reporting.events');
    }

    public function test_revenue_report_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reporting.revenue'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reporting.revenue');
    }

    public function test_export_csv_works(): void
    {
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.reporting.export', [
            'type' => 'users',
            'period' => '30days',
            'format' => 'csv'
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_conversion_funnel_calculation(): void
    {
        $category = EventCategory::factory()->create();
        $event = Event::factory()->create([
            'organizer_id' => $this->organizer->id,
            'category_id' => $category->id,
        ]);
        Booking::factory()->count(2)->create([
            'event_id' => $event->id,
            'payment_status' => 'paid',
        ]);
        Booking::factory()->count(3)->create([
            'event_id' => $event->id,
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reporting.index'));

        $funnel = $response->viewData('conversionFunnel');

        $this->assertEquals(5, $funnel['bookings_started']);
        $this->assertEquals(2, $funnel['bookings_completed']);
        $this->assertEquals(40, $funnel['completion_rate']);
    }
}

