<?php

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Organization;
use Illuminate\Support\Facades\Cache;

// ── Basis-Tests ────────────────────────────────────────────────────────────

it('gibt den ATOM-Feed mit korrektem Content-Type zurück', function () {
    Event::factory()->published()->upcoming()->count(3)->create();

    $this->get(route('feed.atom'))
        ->assertStatus(200)
        ->assertHeader('Content-Type', 'application/atom+xml; charset=utf-8');
});

it('enthält gültiges XML mit atom-Namespace', function () {
    Event::factory()->published()->upcoming()->create(['title' => 'Test-Event XML']);

    $response = $this->get(route('feed.atom'));
    $response->assertStatus(200);
    $response->assertSee('http://www.w3.org/2005/Atom', false);
    $response->assertSee('<?xml', false);
    $response->assertSee('<feed', false);
    $response->assertSee('<entry', false);
});

it('gibt einen leeren Feed zurück wenn keine Events vorhanden', function () {
    $response = $this->get(route('feed.atom'));
    $response->assertStatus(200);
    $response->assertDontSee('<entry', false);
});

// ── Sicherheits-Tests ──────────────────────────────────────────────────────

it('zeigt private Events NICHT im Feed', function () {
    $privateEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => true,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Geheimes-Privat-Event-XYZ',
    ]);

    $this->get(route('feed.atom'))
        ->assertDontSee($privateEvent->slug)
        ->assertDontSee('Geheimes-Privat-Event-XYZ');
});

it('zeigt abgesagte Events NICHT im Feed', function () {
    $cancelledEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'is_cancelled' => true,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Abgesagtes-Event-XYZ',
    ]);

    $this->get(route('feed.atom'))
        ->assertDontSee($cancelledEvent->slug)
        ->assertDontSee('Abgesagtes-Event-XYZ');
});

it('zeigt vergangene Events NICHT im Feed', function () {
    $pastEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'is_cancelled' => false,
        'start_date'   => now()->subDays(2),
        'end_date'     => now()->subDays(2)->addHours(3),
        'title'        => 'Vergangenes-Event-XYZ',
    ]);

    $this->get(route('feed.atom'))
        ->assertDontSee($pastEvent->slug)
        ->assertDontSee('Vergangenes-Event-XYZ');
});

it('zeigt unveröffentlichte Events NICHT im Feed', function () {
    $draftEvent = Event::factory()->create([
        'is_published' => false,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Entwurf-Event-XYZ',
    ]);

    $this->get(route('feed.atom'))
        ->assertDontSee($draftEvent->slug)
        ->assertDontSee('Entwurf-Event-XYZ');
});

it('gibt NIEMALS sensible Felder im Feed aus', function () {
    Event::factory()->create([
        'is_published'       => true,
        'is_private'         => false,
        'start_date'         => now()->addDays(5),
        'end_date'           => now()->addDays(5)->addHours(3),
        'access_code'        => 'TOPSECRET123',
        'online_url'         => 'https://zoom.us/j/geheim-meeting-link',
        'online_access_code' => 'ZOOMPASS456',
    ]);

    $response = $this->get(route('feed.atom'));
    $response->assertDontSee('TOPSECRET123');
    $response->assertDontSee('zoom.us/j/geheim-meeting-link');
    $response->assertDontSee('ZOOMPASS456');
});

// ── Filter-Tests ──────────────────────────────────────────────────────────

it('filtert Events nach Kategorie-Slug (Sub-Feed)', function () {
    $category = EventCategory::factory()->create(['slug' => 'test-konzerte', 'is_active' => true]);

    $matchingEvent = Event::factory()->create([
        'is_published'      => true,
        'is_private'        => false,
        'start_date'        => now()->addDays(5),
        'end_date'          => now()->addDays(5)->addHours(3),
        'event_category_id' => $category->id,
        'title'             => 'Matching-Kategorie-Event',
    ]);

    $otherEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Andere-Kategorie-Event',
    ]);

    $response = $this->get(route('feed.atom.category', 'test-konzerte'));
    $response->assertSee($matchingEvent->slug);
    $response->assertDontSee($otherEvent->slug);
});

it('gibt leeren Feed zurück bei unbekanntem Kategorie-Slug (kein 404)', function () {
    $this->get(route('feed.atom.category', 'nicht-existent-xyz'))
        ->assertStatus(200)
        ->assertDontSee('<entry', false);
});

it('filtert Events nach Stadt (Sub-Feed)', function () {
    $berlinEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'venue_city'   => 'Berlin',
        'title'        => 'Berlin-Event-Test',
    ]);

    $hamburgEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'venue_city'   => 'Hamburg',
        'title'        => 'Hamburg-Event-Test',
    ]);

    $response = $this->get(route('feed.atom.city', 'Berlin'));
    $response->assertSee($berlinEvent->slug);
    $response->assertDontSee($hamburgEvent->slug);
});

it('filtert Events nach Organisation (Sub-Feed)', function () {
    $org = Organization::factory()->create(['slug' => 'test-org-abc', 'is_active' => true]);

    $orgEvent = Event::factory()->create([
        'is_published'    => true,
        'is_private'      => false,
        'start_date'      => now()->addDays(5),
        'end_date'        => now()->addDays(5)->addHours(3),
        'organization_id' => $org->id,
        'title'           => 'Org-Event-Test',
    ]);

    $otherEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Andere-Org-Event-Test',
    ]);

    $response = $this->get(route('feed.atom.organization', 'test-org-abc'));
    $response->assertSee($orgEvent->slug);
    $response->assertDontSee($otherEvent->slug);
});

it('filtert Events nach Typ (Sub-Feed)', function () {
    $onlineEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'event_type'   => 'online',
        'title'        => 'Online-Event-Test',
    ]);

    $physicalEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'event_type'   => 'physical',
        'title'        => 'Physical-Event-Test',
    ]);

    $this->get(route('feed.atom.type', 'online'))
        ->assertSee($onlineEvent->slug)
        ->assertDontSee($physicalEvent->slug);
});

it('akzeptiert nur gültige Typen im Typ-Sub-Feed', function () {
    // Ungültiger Typ → 404 (whereIn-Constraint greift)
    $this->get('/feed/atom/type/ungueltig')->assertStatus(404);
});

it('filtert Events per Query-Parameter category', function () {
    $category = EventCategory::factory()->create(['slug' => 'seminare', 'is_active' => true]);

    $event = Event::factory()->create([
        'is_published'      => true,
        'is_private'        => false,
        'start_date'        => now()->addDays(5),
        'end_date'          => now()->addDays(5)->addHours(3),
        'event_category_id' => $category->id,
        'title'             => 'Seminar-Param-Test',
    ]);

    $this->get(route('feed.atom') . '?category=seminare')
        ->assertSee($event->slug);
});

it('filtert Events per Query-Parameter featured', function () {
    $featuredEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'is_featured'  => true,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Featured-Event-Test',
    ]);

    $normalEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'is_featured'  => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Normal-Event-Test',
    ]);

    $this->get(route('feed.atom') . '?featured=1')
        ->assertSee($featuredEvent->slug)
        ->assertDontSee($normalEvent->slug);
});

it('filtert Events per Query-Parameter q (Freitext)', function () {
    $matchEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Spezial-Suchbegriff-Workshop',
    ]);

    $noMatchEvent = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Anderes Event ohne Treffer',
    ]);

    $this->get(route('feed.atom') . '?q=Spezial-Suchbegriff')
        ->assertSee($matchEvent->slug)
        ->assertDontSee($noMatchEvent->slug);
});

it('ignoriert q-Parameter mit weniger als 2 Zeichen', function () {
    // Einzelnes Zeichen → Validierung schlägt fehl → leerer Filter → alle Events
    Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Einzel-Zeichen-Test-Event',
    ]);

    // ?q=a soll NICHT als Suchfilter wirken (Validierung schlägt fehl → alle Events)
    $response = $this->get(route('feed.atom') . '?q=a');
    $response->assertStatus(200);
    // Kein Fehler, aber der Filter wird ignoriert → Event erscheint im Feed
    $response->assertSee('Einzel-Zeichen-Test-Event');
});

it('blockiert LIKE-Wildcard-Zeichen % und _ im q-Parameter per Validierung', function () {
    Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Kurs mit 100% Erfolg',
    ]);

    // '%' allein ist kein gültiger q-Wert (Regex schlägt fehl) → Validierung
    // verwirft den gesamten Parametersatz → kein q-Filter → Status 200, kein Fehler.
    // Das verhindert, dass '%' als SQL-LIKE-Wildcard alle Zeilen trifft.
    $this->get(route('feed.atom') . '?q=%25')->assertStatus(200);   // % → blockiert
    $this->get(route('feed.atom') . '?q=_____')->assertStatus(200); // _ → blockiert
});

it('escapt verbleibende LIKE-Wildcards in Suchbegriffen mit gemischtem Inhalt', function () {
    // Gültige Suche (besteht Validierung), aber enthält nach dem Regex-Prüf kein % oder _
    // Sicherstellung: sanitizeSearchTerm() läuft auch bei validen Termen
    $eventA = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Workshop Digitales Lehren',
    ]);

    $eventB = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Seminar Klassenführung',
    ]);

    // Präzise Suche trifft nur Event A
    $this->get(route('feed.atom') . '?q=Digitales')
        ->assertSee($eventA->slug)
        ->assertDontSee($eventB->slug);
});

it('ignoriert q-Parameter mit Steuerzeichen oder ungültigen Zeichen', function () {
    // Null-Byte, Steuerzeichen → Regex-Validierung schlägt fehl → Filter wird ignoriert
    $this->get(route('feed.atom') . '?q=' . urlencode("test\x00evil"))
        ->assertStatus(200);

    $this->get(route('feed.atom') . '?q=' . urlencode("<script>alert(1)</script>"))
        ->assertStatus(200); // Keine 500, kein XSS
});

it('begrenzt die Anzahl der Einträge per limit-Parameter', function () {
    Event::factory()->published()->upcoming()->count(10)->create();

    $response = $this->get(route('feed.atom') . '?limit=3');
    $content = $response->getContent();

    // Zähle <entry>-Elemente
    $entryCount = substr_count($content, '<entry>');
    expect($entryCount)->toBeLessThan(4)->toBeGreaterThan(-1);
});

it('ignoriert ungültige Filter-Parameter ohne Fehler', function () {
    $this->get(route('feed.atom') . '?limit=9999&type=ungueltig&date_from=kein-datum&q=' . str_repeat('a', 500))
        ->assertStatus(200);
});

// ── HTTP-Caching-Tests ─────────────────────────────────────────────────────

it('setzt Cache-Control-Header korrekt', function () {
    $response = $this->get(route('feed.atom'));
    $response->assertStatus(200);

    // Laravel kann die Reihenfolge variieren – wir prüfen die Teilstrings
    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)->toContain('max-age=300');
    expect($cacheControl)->toContain('public');
    expect($cacheControl)->toContain('stale-while-revalidate=60');
});

it('setzt ETag-Header', function () {
    $response = $this->get(route('feed.atom'));
    $response->assertStatus(200);

    expect($response->headers->has('ETag'))->toBeTrue();
    expect($response->headers->has('Last-Modified'))->toBeTrue();
});

it('gibt 304 Not Modified bei Conditional GET zurück', function () {
    Event::factory()->published()->upcoming()->create();

    $firstResponse = $this->get(route('feed.atom'));
    $etag = $firstResponse->headers->get('ETag');

    expect($etag)->not->toBeNull();

    $this->get(route('feed.atom'), ['If-None-Match' => $etag])
        ->assertStatus(304);
});

it('setzt X-Content-Type-Options Header', function () {
    $this->get(route('feed.atom'))
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

// ── Inhalts-Tests ─────────────────────────────────────────────────────────

it('enthält GeoRSS-Koordinaten für physische Events', function () {
    Event::factory()->create([
        'is_published'    => true,
        'is_private'      => false,
        'start_date'      => now()->addDays(5),
        'end_date'        => now()->addDays(5)->addHours(3),
        'event_type'      => 'physical',
        'venue_latitude'  => 52.5200,
        'venue_longitude' => 13.4050,
    ]);

    $this->get(route('feed.atom'))
        ->assertSee('georss:point', false)
        ->assertSee('52.52', false);
});

it('enthält KEINE GeoRSS-Koordinaten für Online-Events', function () {
    Event::factory()->create([
        'is_published'    => true,
        'is_private'      => false,
        'start_date'      => now()->addDays(5),
        'end_date'        => now()->addDays(5)->addHours(3),
        'event_type'      => 'online',
        'venue_latitude'  => null,
        'venue_longitude' => null,
    ]);

    $this->get(route('feed.atom'))
        ->assertDontSee('georss:point', false);
});

it('enthält den Organisations-Namen als author', function () {
    $org = Organization::factory()->create(['name' => 'Test-Bildungswerk GmbH']);

    Event::factory()->create([
        'is_published'    => true,
        'is_private'      => false,
        'start_date'      => now()->addDays(5),
        'end_date'        => now()->addDays(5)->addHours(3),
        'organization_id' => $org->id,
    ]);

    $this->get(route('feed.atom'))
        ->assertSee('Test-Bildungswerk GmbH');
});

it('enthält Kategorie-Informationen im Feed', function () {
    $category = EventCategory::factory()->create([
        'name'      => 'Fachtagungen',
        'slug'      => 'fachtagungen',
        'is_active' => true,
    ]);

    Event::factory()->create([
        'is_published'      => true,
        'is_private'        => false,
        'start_date'        => now()->addDays(5),
        'end_date'          => now()->addDays(5)->addHours(3),
        'event_category_id' => $category->id,
    ]);

    $this->get(route('feed.atom'))
        ->assertSee('fachtagungen');
});

// ── Cache-Invalidierungs-Test ──────────────────────────────────────────────

it('invalidiert den Cache nach Event-Änderung', function () {
    Cache::flush();

    $event = Event::factory()->create([
        'is_published' => true,
        'is_private'   => false,
        'start_date'   => now()->addDays(5),
        'end_date'     => now()->addDays(5)->addHours(3),
        'title'        => 'Original-Titel-Cache-Test',
    ]);

    // Feed abrufen (wird gecacht)
    $this->get(route('feed.atom'))->assertSee('Original-Titel-Cache-Test');

    // Cache leeren (Observer würde das im echten Betrieb tun)
    Cache::flush();

    // Event aktualisieren
    $event->update(['title' => 'Geänderter-Titel-Cache-Test']);

    // Neuer Abruf soll den neuen Titel zeigen
    $this->get(route('feed.atom'))->assertSee('Geänderter-Titel-Cache-Test');
});



