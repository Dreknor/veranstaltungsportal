<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_view_all_events()
    {
        $admin = createAdmin();
        Event::factory()->count(10)->create();

        $response = $this->actingAs($admin)->get(route('admin.events.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_all_bookings()
    {
        $admin = createAdmin();
        Booking::factory()->count(15)->create();

        $response = $this->actingAs($admin)->get(route('admin.bookings.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_view_all_users()
    {
        $admin = createAdmin();
        User::factory()->count(20)->create();

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_approve_event()
    {
        $admin = createAdmin();
        $event = Event::factory()->create(['is_published' => false]);

        $response = $this->actingAs($admin)->post(route('admin.events.approve', $event));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_published' => true,
        ]);
    }

    /** @test */
    public function admin_can_reject_event()
    {
        $admin = createAdmin();
        $event = Event::factory()->create(['is_published' => true]);

        $response = $this->actingAs($admin)->post(route('admin.events.reject', $event));

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_published' => false,
        ]);
    }

    /** @test */
    public function admin_can_delete_any_event()
    {
        $admin = createAdmin();
        $organizer = createOrganizer();
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $response = $this->actingAs($admin)->delete(route('admin.events.destroy', $event));

        $this->assertSoftDeleted('events', ['id' => $event->id]);
    }

    /** @test */
    public function admin_can_view_platform_settings()
    {
        $admin = createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_update_platform_settings()
    {
        $admin = createAdmin();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'platform_fee_percentage' => 5.5,
            'platform_name' => 'Event Platform',
            'contact_email' => 'admin@platform.com',
        ]);

        $response->assertStatus(302);
    }

    /** @test */
    public function admin_can_manage_event_categories()
    {
        $admin = createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'New Category',
            'description' => 'Category description',
        ]);

        $this->assertDatabaseHas('event_categories', [
            'name' => 'New Category',
        ]);
    }

    /** @test */
    public function admin_can_view_revenue_statistics()
    {
        $admin = createAdmin();

        Booking::factory()->count(20)->create([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'total' => 100,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.revenue'));

        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cannot_access_admin_panel()
    {
        $user = createUser();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_ban_user()
    {
        $admin = createAdmin();
        $user = User::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.users.ban', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_banned' => true,
        ]);
    }

    /** @test */
    public function admin_can_feature_event()
    {
        $admin = createAdmin();
        $event = Event::factory()->create(['is_featured' => false]);

        $response = $this->actingAs($admin)->post(route('admin.events.feature', $event), [
            'is_featured' => true,
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'is_featured' => true,
        ]);
    }
}

