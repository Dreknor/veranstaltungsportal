<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataPrivacyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        Storage::fake('public');
    }

    public function test_data_privacy_index_requires_authentication(): void
    {
        $response = $this->get(route('data-privacy.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_data_privacy_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('data-privacy.index'));

        $response->assertStatus(200);
        $response->assertViewIs('data-privacy.index');
    }

    public function test_user_can_export_their_data(): void
    {
        // Create some data for the user
        $category = EventCategory::factory()->create();
        $event = Event::factory()->create(['event_category_id' => $category->id]);
        Booking::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('data-privacy.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/json');

        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('bookings', $data);
        $this->assertEquals($this->user->email, $data['user']['email']);
    }

    public function test_export_logs_audit_entry(): void
    {
        $this->actingAs($this->user)->get(route('data-privacy.export'));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'action' => 'data_export',
        ]);
    }

    public function test_user_can_download_files(): void
    {
        $response = $this->actingAs($this->user)->get(route('data-privacy.download-files'));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');
    }

    public function test_user_can_request_account_deletion(): void
    {
        $response = $this->actingAs($this->user)->post(route('data-privacy.request-deletion'), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        // User should be soft deleted
        $this->assertSoftDeleted('users', ['id' => $this->user->id]);
    }

    public function test_deletion_requires_correct_password(): void
    {
        $response = $this->actingAs($this->user)->post(route('data-privacy.request-deletion'), [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password');
        // User should still exist and not be deleted
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'email' => $this->user->email]);
        $this->user->refresh();
        $this->assertNull($this->user->deleted_at);
    }

    public function test_cannot_delete_account_with_upcoming_events_as_organizer(): void
    {
        $result = $this->createOrganizerWithOrganization($this->user);
        $organization = $result['organization'];

        Event::factory()->create([
            'organization_id' => $organization->id,
            'start_date' => now()->addDays(10),
        ]);

        $response = $this->actingAs($this->user)->post(route('data-privacy.request-deletion'), [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        // User should still exist and not be deleted
        $this->assertDatabaseHas('users', ['id' => $this->user->id, 'email' => $this->user->email]);
        $this->user->refresh();
        $this->assertNull($this->user->deleted_at);
    }

    public function test_cannot_delete_account_with_upcoming_bookings(): void
    {
        $event = Event::factory()->create([
            'start_date' => now()->addDays(10),
        ]);

        Booking::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('data-privacy.request-deletion'), [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_deletion_logs_audit_entry(): void
    {
        $this->actingAs($this->user)->post(route('data-privacy.request-deletion'), [
            'password' => 'password',
            'reason' => 'No longer needed',
        ]);

        // Check that audit log was created with correct action and description
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'account_deletion_requested',
        ]);

        $auditLog = \App\Models\AuditLog::where('action', 'account_deletion_requested')->first();
        $this->assertNotNull($auditLog);
        $this->assertStringContainsString('No longer needed', $auditLog->description ?? '');
    }

    public function test_user_can_view_privacy_settings(): void
    {
        $response = $this->actingAs($this->user)->get(route('data-privacy.settings'));

        $response->assertStatus(200);
        $response->assertViewIs('data-privacy.settings');
    }

    public function test_user_can_update_privacy_settings(): void
    {
        $response = $this->actingAs($this->user)->put(route('data-privacy.settings.update'), [
            'allow_networking' => true,
            'show_profile_public' => false,
            'allow_data_analytics' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertTrue($this->user->allow_networking);
        $this->assertFalse($this->user->show_profile_public);
        $this->assertTrue($this->user->allow_data_analytics);
    }

    public function test_privacy_settings_update_logs_audit_entry(): void
    {
        $this->actingAs($this->user)->put(route('data-privacy.settings.update'), [
            'allow_networking' => true,
            'show_profile_public' => true,
            'allow_data_analytics' => true,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'action' => 'privacy_settings_updated',
        ]);
    }

    public function test_exported_data_includes_all_user_information(): void
    {
        $event = Event::factory()->create();

        Booking::factory()->create([
            'user_id' => $this->user->id,
            'event_id' => $event->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('data-privacy.export'));
        $data = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('user', $data);
        $this->assertArrayHasKey('bookings', $data);
        $this->assertArrayHasKey('organized_events', $data);
        $this->assertArrayHasKey('favorites', $data);
        $this->assertArrayHasKey('notifications', $data);
        $this->assertArrayHasKey('audit_logs', $data);
    }
}

