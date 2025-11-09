<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_profile_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
    }

    /** @test */
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

        $response = $this->actingAs($user)->patch(route('profile.update'), $updateData);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function organizer_can_update_organization_information()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);

        $updateData = [
            'name' => $organizer->name,
            'email' => $organizer->email,
            'organization_name' => 'New Organization',
            'organization_website' => 'https://neworg.com',
            'organization_description' => 'A new organization description',
        ];

        $response = $this->actingAs($organizer)->patch(route('profile.update'), $updateData);

        $this->assertDatabaseHas('users', [
            'id' => $organizer->id,
            'organization_name' => 'New Organization',
        ]);
    }

    /** @test */
    public function user_can_update_billing_information()
    {
        $user = User::factory()->create();

        $billingData = [
            'name' => $user->name,
            'email' => $user->email,
            'billing_company' => 'Test Company',
            'billing_address' => '123 Test St',
            'billing_postal_code' => '12345',
            'billing_city' => 'Berlin',
            'billing_country' => 'Germany',
            'tax_id' => 'DE123456789',
        ];

        $response = $this->actingAs($user)->patch(route('profile.update'), $billingData);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'billing_company' => 'Test Company',
            'tax_id' => 'DE123456789',
        ]);
    }

    /** @test */
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

        $response = $this->actingAs($user)->patch(route('profile.update'), $updateData);

        $user->refresh();
        $this->assertTrue($user->notification_preferences['email_booking_confirmation']);
    }

    /** @test */
    public function user_cannot_use_duplicate_email()
    {
        $user1 = User::factory()->create(['email' => 'existing@example.com']);
        $user2 = User::factory()->create();

        $updateData = [
            'name' => $user2->name,
            'email' => 'existing@example.com',
        ];

        $response = $this->actingAs($user2)->patch(route('profile.update'), $updateData);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function user_can_delete_account()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
