<?php

use App\Http\Controllers\HomeController;
use App\Models\Event;
use App\Models\EventCategory;

test('Empfohlene Veranstaltungen erscheinen nicht noch einmal unter „Kommende"', function () {
    $category = EventCategory::factory()->create(['is_active' => true]);
    $base = ['is_published' => true, 'event_category_id' => $category->id];

    // Die empfohlene Veranstaltung liegt zeitlich vorne und wäre sonst auch die erste „kommende"
    $featured = Event::factory()->create($base + [
        'title' => 'Empfohlener Fachtag',
        'is_featured' => true,
        'start_date' => now()->addDay(),
        'end_date' => now()->addDay()->addHours(2),
    ]);

    foreach (range(2, 9) as $day) {
        Event::factory()->create($base + [
            'start_date' => now()->addDays($day),
            'end_date' => now()->addDays($day)->addHours(2),
        ]);
    }

    $response = $this->get(route('home'))->assertOk();

    expect($response->viewData('featuredEvents')->modelKeys())->toBe([$featured->id]);
    $upcoming = $response->viewData('upcomingEvents');
    expect($upcoming->modelKeys())->not->toContain($featured->id)
        ->and($upcoming)->toHaveCount(HomeController::UPCOMING_LIMIT);

    $response->assertSeeInOrder(['Empfohlene Veranstaltungen', 'Empfohlener Fachtag', 'Kommende Veranstaltungen']);
});

test('Startseite zeigt nur die acht Kategorien mit den meisten kommenden Veranstaltungen', function () {
    $counts = [9, 8, 7, 6, 5, 4, 3, 2, 1, 0];
    $categories = [];

    foreach ($counts as $i => $count) {
        $category = EventCategory::factory()->create(['name' => "Kategorie {$i}", 'is_active' => true]);
        $categories[] = $category;

        foreach (range(1, $count) as $n) {
            if ($count === 0) {
                break;
            }
            Event::factory()->create([
                'event_category_id' => $category->id,
                'is_published' => true,
                'start_date' => now()->addDays($n),
                'end_date' => now()->addDays($n)->addHours(2),
            ]);
        }
    }

    // Vergangene Veranstaltungen zählen nicht: Kategorie 9 bleibt außen vor
    Event::factory()->count(20)->create([
        'event_category_id' => $categories[9]->id,
        'is_published' => true,
        'start_date' => now()->subWeek(),
        'end_date' => now()->subWeek()->addHours(2),
    ]);

    // Inaktive Kategorien erscheinen nie
    $inactive = EventCategory::factory()->create(['name' => 'Inaktiv', 'is_active' => false]);
    Event::factory()->count(12)->create([
        'event_category_id' => $inactive->id,
        'is_published' => true,
        'start_date' => now()->addWeek(),
        'end_date' => now()->addWeek()->addHours(2),
    ]);

    $response = $this->get(route('home'))->assertOk();

    $shown = $response->viewData('categories');
    expect($shown)->toHaveCount(HomeController::CATEGORY_LIMIT)
        ->and($shown->pluck('name')->all())->toBe(array_map(fn ($i) => "Kategorie {$i}", range(0, 7)));

    $response->assertSeeInOrder(['Kategorien', 'Alle Kategorien', 'Kategorie 0', 'Kategorie 7'])
        ->assertSee('href="'.route('events.index').'"', false)
        ->assertDontSee('href="'.route('events.index', ['category' => $categories[8]->id]).'"', false)
        ->assertDontSee('href="'.route('events.index', ['category' => $inactive->id]).'"', false);
});

it('shows the search row without the filter button on the start page', function () {
    $html = $this->get('/')->assertOk()->getContent();

    expect($html)->toContain('name="format"')
        ->and($html)->toContain('name="zeitraum"')
        ->and($html)->not->toContain('name="city"')
        ->and($html)->not->toContain('filtersOpen');
});

it('keeps the filter button on the events page', function () {
    $this->get(route('events.index'))->assertOk()->assertSee('name="city"', false);
});
