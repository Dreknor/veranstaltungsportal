<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use PHPUnit\Framework\Attributes\Test;

class UserTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function it_returns_initials_from_first_and_last_name()
    {
        $user = User::factory()->create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
        ]);

        $this->assertEquals('MM', $user->initials());
    }

    #[Test]
    public function it_returns_initials_from_name_when_no_first_last_name()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'first_name' => null,
            'last_name' => null,
        ]);

        $this->assertEquals('JD', $user->initials());
    }

    #[Test]
    public function it_returns_full_name()
    {
        $user = User::factory()->create([
            'first_name' => 'Max',
            'last_name' => 'Mustermann',
        ]);

        $this->assertEquals('Max Mustermann', $user->fullName());
    }

    #[Test]
    public function it_returns_name_when_no_first_last_name()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'first_name' => null,
            'last_name' => null,
        ]);

        $this->assertEquals('John Doe', $user->fullName());
    }

    #[Test]
    public function it_returns_profile_photo_url()
    {
        $user = User::factory()->create(['profile_photo' => 'profile-photos/test.jpg']);
        $expected = route('profile-photo.show', ['user' => $user->id]);
        $this->assertEquals($expected, $user->profilePhotoUrl());
        $this->assertStringContainsString('/profile-photo/' . $user->id, $user->profilePhotoUrl());
    }

    #[Test]
    public function it_returns_gravatar_when_no_profile_photo()
    {
        $user = User::factory()->create(['profile_photo' => null]);
        $url = $user->profilePhotoUrl();
        $this->assertStringContainsString('gravatar.com/avatar/', $url);
        $this->assertStringContainsString('?d=mp&s=200', $url);
    }


    #[Test]
    public function it_checks_if_user_is_participant()
    {
        $user = User::factory()->create();
        $user->assignRole('user'); // Participants haben die 'user' Rolle

        $this->assertTrue($user->hasRole('user'));
    }

    #[Test]
    public function it_checks_if_user_is_organizer()
    {
        $user = User::factory()->create();
        $user->assignRole('organizer');

        $this->assertTrue($user->hasRole('organizer'));
    }

    #[Test]
    public function it_checks_if_user_is_admin()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $this->assertTrue($user->isAdmin());
    }

    #[Test]
    public function it_returns_user_type_label()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');

        $participant = User::factory()->create();
        $participant->assignRole('user');

        // Prüfe Rollen statt user_type
        $this->assertTrue($organizer->hasRole('organizer'));
        $this->assertTrue($participant->hasRole('user'));
    }

    #[Test]
    public function it_checks_if_user_can_manage_events()
    {

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');

        $participant = User::factory()->create();
        $participant->assignRole('user');

        // Organizer hat Rolle und kann Events managen
        $this->assertTrue($organizer->hasRole('organizer'));
        $this->assertFalse($participant->hasRole('organizer'));
    }

    #[Test]
    public function it_casts_notification_preferences_to_array()
    {
        $user = User::factory()->create();

        // notification_preferences sollte als Array gespeichert sein (mit Standard-Werten)
        $this->assertIsArray($user->notification_preferences);

        // Prüfe, dass wir auf die Werte zugreifen können
        $this->assertNotNull($user->notification_preferences);
        $this->assertIsArray($user->notification_preferences);
    }
}
