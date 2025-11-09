<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_events()
    {
        $user = User::factory()->create();
        Event::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->events);
    }

    /** @test */
    public function it_has_bookings()
    {
        $user = User::factory()->create();
        Booking::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->bookings);
    }

    /** @test */
    public function it_returns_initials_from_first_and_last_name()
    {
        $user = User::factory()->create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
        ]);

        $this->assertEquals('MM', $user->initials());
    }

    /** @test */
    public function it_returns_initials_from_name_when_no_first_last_name()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'first_name' => null,
            'last_name' => null,
        ]);

        $this->assertEquals('JD', $user->initials());
    }

    /** @test */
    public function it_returns_full_name()
    {
        $user = User::factory()->create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
        ]);

        $this->assertEquals('Max Mustermann', $user->fullName());
    }

    /** @test */
    public function it_returns_name_when_no_first_last_name()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'first_name' => null,
            'last_name' => null,
        ]);

        $this->assertEquals('John Doe', $user->fullName());
    }

    /** @test */
    public function it_returns_profile_photo_url()
    {
        $user = User::factory()->create(['profile_photo' => 'photos/test.jpg']);

        $this->assertStringContainsString('photos/test.jpg', $user->profilePhotoUrl());
    }

    /** @test */
    public function it_returns_gravatar_when_no_profile_photo()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'profile_photo' => null,
        ]);

        $this->assertStringContainsString('gravatar.com', $user->profilePhotoUrl());
    }

    /** @test */
    public function it_checks_if_user_is_organizer()
    {
        $user = User::factory()->create(['user_type' => 'organizer']);

        $this->assertTrue($user->isOrganizer());
    }

    /** @test */
    public function it_checks_if_user_is_participant()
    {
        $user = User::factory()->create(['user_type' => 'participant']);

        $this->assertTrue($user->isParticipant());
    }

    /** @test */
    public function it_checks_if_user_is_admin()
    {
        $user = User::factory()->create();
        $adminRole = Role::create(['name' => 'admin']);
        $user->assignRole($adminRole);

        $this->assertTrue($user->isAdmin());
    }

    /** @test */
    public function it_returns_user_type_label()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $participant = User::factory()->create(['user_type' => 'participant']);

        $this->assertEquals('Organisator', $organizer->userTypeLabel());
        $this->assertEquals('Teilnehmer', $participant->userTypeLabel());
    }

    /** @test */
    public function it_checks_if_user_can_manage_events()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $participant = User::factory()->create(['user_type' => 'participant']);

        $this->assertTrue($organizer->canManageEvents());
        $this->assertFalse($participant->canManageEvents());
    }

    /** @test */
    public function it_casts_notification_preferences_to_array()
    {
        $user = User::factory()->create([
            'notification_preferences' => ['email' => true, 'sms' => false],
        ]);

        $this->assertIsArray($user->notification_preferences);
        $this->assertTrue($user->notification_preferences['email']);
        $this->assertFalse($user->notification_preferences['sms']);
    }
}

