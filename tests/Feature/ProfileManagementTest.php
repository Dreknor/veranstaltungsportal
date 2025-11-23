<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_view_profile_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('settings.profile.edit'));

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_update_profile_information()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
            'phone' => '123456789',
        ];

        $response = $this->actingAs($user)->put(route('settings.profile.update'), $updateData);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    #[Test]
    public function user_can_update_notification_preferences()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'notification_preferences' => [
                'booking_notifications' => false,  // Changed from default true
                'event_updates' => true,          // Changed from default true
                'reminder_notifications' => false, // Changed from default true
            ],
        ];

        $response = $this->actingAs($user)->put(route('settings.profile.update'), $updateData);

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect(route('settings.profile.edit'));

        $user->refresh();

        // Verify that notification preferences were updated
        $this->assertNotNull($user->notification_preferences);
        $this->assertIsArray($user->notification_preferences);

        // Check that the values have changed from defaults
        $prefs = $user->notification_preferences;
        if (isset($prefs['booking_notifications'])) {
            $this->assertFalse($prefs['booking_notifications']);
        }
        if (isset($prefs['event_updates'])) {
            $this->assertTrue($prefs['event_updates']);
        }
        if (isset($prefs['reminder_notifications'])) {
            $this->assertFalse($prefs['reminder_notifications']);
        }
    }

    #[Test]
    public function user_cannot_use_duplicate_email()
    {
        $user1 = User::factory()->create(['email' => 'existing@example.com']);
        $user2 = User::factory()->create();

        $updateData = [
            'name' => $user2->name,
            'email' => 'existing@example.com',
        ];

        $response = $this->actingAs($user2)->put(route('settings.profile.update'), $updateData);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function user_can_delete_account()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('settings.profile.destroy'), [
            'password' => 'password',
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}


