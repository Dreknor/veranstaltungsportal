<?php

use App\Models\Event;
use Carbon\CarbonImmutable;

function searchEvent(string $title, array $attributes = []): Event
{
    $start = $attributes['start_date'] ?? now()->addWeek();

    return Event::factory()->create(array_merge([
        'title' => $title,
        'is_published' => true,
        'start_date' => $start,
        'end_date' => (clone $start)->addHours(2),
    ], $attributes));
}

/** Titel der Treffer auf der Übersicht */
function resultTitles($response): array
{
    return collect($response->viewData('items')->items())
        ->map(fn ($item) => $item['item']->title)
        ->sort()
        ->values()
        ->all();
}

test('Format filtert nach Präsenz, Online und Hybrid', function () {
    searchEvent('Präsenzkurs', ['event_type' => 'physical']);
    searchEvent('Onlinekurs', ['event_type' => 'online']);
    searchEvent('Hybridkurs', ['event_type' => 'hybrid']);
    searchEvent('Externer Kurs', ['event_type' => 'external']);

    expect(resultTitles($this->get(route('events.index', ['format' => 'praesenz']))))->toBe(['Präsenzkurs']);
    expect(resultTitles($this->get(route('events.index', ['format' => 'präsenz']))))->toBe(['Präsenzkurs']);
    expect(resultTitles($this->get(route('events.index', ['format' => 'online']))))->toBe(['Onlinekurs']);
    expect(resultTitles($this->get(route('events.index', ['format' => 'hybrid']))))->toBe(['Hybridkurs']);

    $this->get(route('events.index', ['format' => 'online']))
        ->assertOk()
        ->assertSee('Format: Online')
        ->assertSee('<option value="online" selected', false);
});

test('Unbekanntes Format wird ignoriert', function () {
    searchEvent('Präsenzkurs', ['event_type' => 'physical']);
    searchEvent('Onlinekurs', ['event_type' => 'online']);

    $response = $this->get(route('events.index', ['format' => 'physical; drop table events']));

    $response->assertOk()->assertDontSee('Format:');
    expect(resultTitles($response))->toBe(['Onlinekurs', 'Präsenzkurs']);

    $response = $this->get(route('events.index', ['format' => ['online']]));
    $response->assertOk();
    expect(resultTitles($response))->toBe(['Onlinekurs', 'Präsenzkurs']);
});

test('Zeitraum filtert nach heute, dieser Woche, diesem Monat und den nächsten drei Monaten', function () {
    // Mittwoch, 18.11.2026, 10:00 Uhr
    $this->travelTo(CarbonImmutable::parse('2026-11-18 10:00:00'));

    searchEvent('Heute', ['start_date' => now()->setTime(15, 0)]);
    searchEvent('Samstag', ['start_date' => now()->setDate(2026, 11, 21)->setTime(9, 0)]);
    searchEvent('Ende November', ['start_date' => now()->setDate(2026, 11, 28)->setTime(9, 0)]);
    searchEvent('Dezember', ['start_date' => now()->setDate(2026, 12, 20)->setTime(9, 0)]);
    searchEvent('Mitte Februar', ['start_date' => now()->setDate(2027, 2, 17)->setTime(9, 0)]);
    searchEvent('März', ['start_date' => now()->setDate(2027, 3, 10)->setTime(9, 0)]);

    expect(resultTitles($this->get(route('events.index', ['zeitraum' => 'heute']))))
        ->toBe(['Heute']);
    expect(resultTitles($this->get(route('events.index', ['zeitraum' => 'diese-woche']))))
        ->toBe(['Heute', 'Samstag']);
    expect(resultTitles($this->get(route('events.index', ['zeitraum' => 'dieser-monat']))))
        ->toBe(['Ende November', 'Heute', 'Samstag']);
    expect(resultTitles($this->get(route('events.index', ['zeitraum' => 'naechste-3-monate']))))
        ->toBe(['Dezember', 'Ende November', 'Heute', 'Mitte Februar', 'Samstag']);

    $this->get(route('events.index', ['zeitraum' => 'dieser-monat']))
        ->assertSee('Zeitraum: Diesen Monat');

    // Unbekannter Zeitraum wird ignoriert
    expect(resultTitles($this->get(route('events.index', ['zeitraum' => 'naechstes-jahr']))))->toHaveCount(6);
});

test('Von und Bis haben Vorrang vor dem Zeitraum', function () {
    $this->travelTo(CarbonImmutable::parse('2026-11-18 10:00:00'));

    searchEvent('Heute', ['start_date' => now()->setTime(15, 0)]);
    searchEvent('März', ['start_date' => now()->setDate(2027, 3, 10)->setTime(9, 0)]);

    $response = $this->get(route('events.index', [
        'zeitraum' => 'heute',
        'date_from' => '2027-03-01',
    ]));

    expect(resultTitles($response))->toBe(['März']);
    $response->assertDontSee('Zeitraum: Heute')->assertSee('ab 01.03.2027');
});

test('Seitennavigation behält Format und Zeitraum', function () {
    foreach (range(1, 13) as $i) {
        searchEvent("Onlinekurs {$i}", ['event_type' => 'online', 'start_date' => now()->addDays($i)]);
    }

    $response = $this->get(route('events.index', ['format' => 'online', 'zeitraum' => 'naechste-3-monate']));

    $next = $response->viewData('items')->nextPageUrl();
    expect($next)->toContain('format=online')->toContain('zeitraum=naechste-3-monate')->toContain('page=2');
});

test('Filter-Zähler zählt Kategorie, Ort und Datum, nicht Format und Zeitraum', function () {
    $this->get(route('events.index', ['format' => 'online', 'zeitraum' => 'heute', 'city' => 'Dresden', 'date_to' => '2027-01-01']))
        ->assertOk()
        ->assertSeeInOrder(['Filter', '>2<', 'aktiv'], false);
});
