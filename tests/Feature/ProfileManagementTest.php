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
                'email_booking_confirmation' => true,
                'email_event_reminder' => true,
                'email_event_updates' => false,
            ],
        ];

        $response = $this->actingAs($user)->put(route('settings.profile.update'), $updateData);

        $user->refresh();
        $this->assertTrue($user->notification_preferences['email_booking_confirmation']);
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


