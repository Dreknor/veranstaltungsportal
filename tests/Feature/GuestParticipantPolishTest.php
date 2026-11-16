<?php

use App\Models\Badge;
use App\Models\Booking;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

// Nacharbeit Gast/Teilnehmende: Markdown-Beschreibung, Buchungsseite, Einstellungen, Abzeichen, Fuß.

test('Markdown-Vorschau rendert sicher und nur für Angemeldete', function () {
    $this->postJson(route('markdown.preview'), ['text' => '**fett**'])->assertUnauthorized();

    $this->actingAs(User::factory()->create())
        ->postJson(route('markdown.preview'), ['text' => "**Inhalte:**\n- eins\n<script>alert(1)</script>\n[x](javascript:alert(1))"])
        ->assertOk()
        ->assertJsonPath('html', fn (string $html) => str_contains($html, '<strong>Inhalte:</strong>')
            && str_contains($html, '<li>eins')
            && ! str_contains($html, '<script')
            && ! str_contains($html, 'javascript:'));
});

test('Veranstaltung anlegen zeigt das Markdown-Eingabefeld für die Beschreibung', function () {
    $organizer = User::factory()->create();
    $this->createOrganizerWithOrganization($organizer);

    $this->actingAs($organizer)
        ->get(route('organizer.events.create'))
        ->assertOk()
        ->assertSee('x-data="markdownEditor(', false)
        ->assertSee('role="toolbar"', false)
        ->assertSee('name="description"', false)
        ->assertSeeInOrder(['Fett', 'Kursiv', 'Überschrift', 'Aufzählung', 'Nummerierung', 'Link', 'Vorschau'])
        ->assertSee('markdown\\/preview', false)
        ->assertSee('value="Deutschland"', false);
});

test('Detailseite zeigt die Beschreibung als Markdown, Land deutsch und kostenfreie Tickets', function () {
    $event = Event::factory()->published()->create([
        'description' => "**Inhalte:**\n- Grundlagen\n- Übungen\n<script>alert(1)</script>",
        'event_type' => 'physical',
        'venue_name' => 'Gemeindehaus',
        'venue_city' => 'Radebeul',
        'venue_country' => 'Germany',
        'start_date' => now()->addWeek(),
        'end_date' => now()->addWeek()->addHours(2),
    ]);
    TicketType::factory()->for($event)->create(['name' => 'Freiplatz', 'price' => 0, 'quantity' => 10]);

    $this->get(route('events.show', $event))
        ->assertOk()
        ->assertSee('<strong>Inhalte:</strong>', false)
        ->assertSee('<li>Grundlagen</li>', false)
        ->assertDontSee('**Inhalte:**')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('<span class="text-ink-soft">Deutschland</span>', false)
        ->assertSeeInOrder(['Freiplatz', 'kostenfrei']);
});

test('Buchungsseite einer kostenpflichtigen Veranstaltung wirkt ohne Auswahl nicht kostenfrei', function () {
    $event = Event::factory()->published()->create(['start_date' => now()->addWeek(), 'end_date' => now()->addWeek()->addHours(2)]);
    TicketType::factory()->for($event)->create(['price' => 69, 'quantity' => 10]);

    $this->get(route('bookings.create', $event))
        ->assertOk()
        ->assertSee('<span id="total">–</span>', false)
        ->assertSee('Kostenpflichtig buchen');
});

test('Einstellungen haben Seitentitel, keine Darstellung und den richtigen Kontotyp', function () {
    $admin = User::factory()->create();
    $admin->assignRole(\Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']));

    $response = $this->actingAs($admin)->get(route('settings.profile.edit'));

    $response->assertOk()
        ->assertSee('<title>Profil', false)
        ->assertDontSee('Darstellung')
        ->assertSee('Administrator')
        ->assertSee('Foto auswählen')
        ->assertSee('Keine Datei ausgewählt');

    $this->actingAs($admin)->get(route('settings.password.edit'))->assertSee('<title>Passwort', false);
    $this->actingAs($admin)->get(route('settings.privacy.edit'))->assertSee('<title>Datenschutz-Einstellungen', false)
        ->assertSee('Nutzungsanalyse')
        ->assertDontSee('Datenverarbeitung &amp; Analytics', false);
    $this->actingAs($admin)->get(route('settings.interests.edit'))->assertSee('<title>Interessen &amp; Newsletter', false);
    $this->actingAs($admin)->get(route('settings.notifications.edit'))->assertSee('E-Mail-Benachrichtigungen');
});

test('Hilfe-Link im Fuß nur für Angemeldete', function () {
    $this->get(route('faq'))->assertOk()->assertDontSee('href="'.route('help.index').'"', false);

    $this->actingAs(User::factory()->create())
        ->get(route('faq'))
        ->assertOk()
        ->assertSee('href="'.route('help.index').'"', false);
});

test('Abzeichen-Seite mit deutschen Zahlen und gedeckeltem Fortschritt', function () {
    $user = User::factory()->create();
    $earned = Badge::factory()->create(['name' => 'Erste Schritte', 'type' => 'achievement', 'points' => 1500, 'requirements' => ['events_attended' => 1]]);
    Badge::factory()->create(['name' => 'Bildungsmarathon', 'type' => 'attendance', 'requirements' => ['total_hours_attended' => 3]]);
    Badge::factory()->create(['name' => 'Dritter', 'type' => 'special', 'requirements' => ['reviews_written' => 5]]);
    $user->awardBadge($earned);

    // 2 Stunden eingecheckt: 2 von 3 Stunden (nicht 120 Minuten)
    $event = Event::factory()->create(['start_date' => now()->subDay(), 'end_date' => now()->subDay()->addHours(2), 'duration' => 120]);
    Booking::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'checked_in' => true]);

    $this->actingAs($user)->get(route('badges.index'))
        ->assertOk()
        ->assertSee('33,3 %')
        ->assertSee('1.500')
        ->assertSeeInOrder(['Fortbildungsstunden:', '2 von', '3'])
        ->assertSee('66,7 %')
        ->assertDontSee('items-center p-4 text-center', false);

    $this->actingAs($user)->get(route('badges.show', $earned))->assertOk()->assertSee('Erfolg')->assertDontSee('Achievement');
});

test('Anstehende Veranstaltungen zeigen ganze Tage', function () {
    $user = User::factory()->create();
    $event = Event::factory()->published()->create(['start_date' => now()->addDays(5)->setTime(10, 0), 'end_date' => now()->addDays(5)->setTime(12, 0)]);
    Booking::factory()->create(['user_id' => $user->id, 'event_id' => $event->id, 'status' => 'confirmed']);

    $this->actingAs($user)->get(route('user.events.upcoming'))
        ->assertOk()
        ->assertSee('In 5 Tagen');
});

test('Follower-Liste zeigt die Zahl besuchter Veranstaltungen', function () {
    $user = User::factory()->create(['show_profile_public' => true]);
    $follower = User::factory()->create();
    \Illuminate\Support\Facades\DB::table('user_connections')->insert([
        'follower_id' => $follower->id,
        'following_id' => $user->id,
        'status' => 'accepted',
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $event = Event::factory()->create();
    Booking::factory()->create(['user_id' => $follower->id, 'event_id' => $event->id, 'checked_in' => true]);

    $this->actingAs($user)->get(route('users.followers', $user))
        ->assertOk()
        ->assertSee('1 besuchte Veranstaltung');
});

test('Benachrichtigungen zeigen deutschen Titel statt „Event-Erinnerung“', function () {
    Notification::fake();
    $user = User::factory()->create();
    $user->notifications()->create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'type' => 'App\Notifications\EventReminderNotification',
        'data' => ['title' => 'Event-Erinnerung', 'message' => 'Erinnerung: "Klassenrat" beginnt bald'],
    ]);

    $this->actingAs($user)->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Erinnerung an Ihre Veranstaltung')
        ->assertSee('„Klassenrat“')
        ->assertDontSee('Event-Erinnerung');
});
