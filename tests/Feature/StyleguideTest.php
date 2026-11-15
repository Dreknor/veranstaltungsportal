<?php

use App\Models\Event;
use App\Models\User;

test('Gäste werden von der Styleguide-Seite zur Anmeldung geleitet', function () {
    $this->get(route('styleguide'))->assertRedirect(route('login'));
});

test('Angemeldete ohne Admin-Rolle erhalten keinen Zugriff auf den Styleguide', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('styleguide'))->assertForbidden();
});

test('Admins sehen den Styleguide ohne Veranstaltungen mit leerem Zustand', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->get(route('styleguide'))
        ->assertOk()
        ->assertSee('Styleguide')
        ->assertSee('Keine Veranstaltungen');
});

test('Admins sehen den Styleguide mit echten Veranstaltungen in Listenzeilen', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $event = Event::factory()->create([
        'title' => 'Klassenführung, die trägt',
        'is_published' => true,
        'start_date' => now()->addWeek()->setTime(14, 0),
        'end_date' => now()->addWeek()->setTime(17, 30),
    ]);

    $this->actingAs($admin)
        ->get(route('styleguide'))
        ->assertOk()
        ->assertSee('Klassenführung, die trägt')
        ->assertSee(route('events.show', $event->slug), false)
        ->assertSee('14:00–17:30 Uhr');
});
