<?php

use App\Models\Event;
use App\Models\EventCategory;

test('Kategorien auf der Startseite zählen nur kommende, veröffentlichte Veranstaltungen', function () {
    $category = EventCategory::factory()->create(['name' => 'Medienbildung', 'is_active' => true]);

    Event::factory()->create(['event_category_id' => $category->id, 'is_published' => true, 'start_date' => now()->addWeek(), 'end_date' => now()->addWeek()->addHours(2)]);
    Event::factory()->create(['event_category_id' => $category->id, 'is_published' => true, 'start_date' => now()->addMonth(), 'end_date' => now()->addMonth()->addHours(2)]);
    // vergangen und unveröffentlicht zählen nicht
    Event::factory()->create(['event_category_id' => $category->id, 'is_published' => true, 'start_date' => now()->subWeek(), 'end_date' => now()->subWeek()->addHours(2)]);
    Event::factory()->create(['event_category_id' => $category->id, 'is_published' => false, 'start_date' => now()->addWeek(), 'end_date' => now()->addWeek()->addHours(2)]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSeeInOrder(['Medienbildung', '2 Veranstaltungen']);

    // Zahl entspricht der Trefferliste hinter dem Kategorie-Link
    expect(Event::listed()->where('event_category_id', $category->id)->count())->toBe(2);
});

test('Startseite hat eine Hauptnavigation, die auf dem Handy ohne Menü-Button auskommt', function () {
    // B4: Unter 768 px rutschen die Links in eine zweite Zeile, es gibt kein Hamburger-Menü.
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('aria-label="Hauptnavigation"', false)
        ->assertSee('href="'.route('events.index').'"', false)
        ->assertSee('href="'.route('login').'"', false)
        ->assertDontSee('aria-controls="public-mobile-menu"', false);
});
