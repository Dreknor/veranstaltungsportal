<?php

/*
 * Stellt sicher, dass zentrale Seiten vollständig im Layout gerendert werden.
 * Hintergrund: Ein falsch verschachteltes @if/@endif in einer View lieferte HTTP 200,
 * aber ohne <head> und Stylesheet (nur der Seiteninhalt kam an).
 */

beforeEach(fn () => setupRoles());

it('rendert die Bearbeitungsseite einer Veranstaltung im vollständigen App-Layout', function () {
    ['organizer' => $organizer, 'organization' => $org] = createOrganizerWithOrganization();
    $event = createEvent(['organization_id' => $org->id, 'user_id' => $organizer->id]);

    $this->actingAs($organizer)
        ->get(route('organizer.events.edit', $event))
        ->assertOk()
        ->assertSee('<html', false)
        ->assertSee('</html>', false)
        ->assertSee('id="inhalt"', false);
});

it('rendert die Anlageseite einer Veranstaltung im vollständigen App-Layout', function () {
    ['organizer' => $organizer] = createOrganizerWithOrganization();

    $this->actingAs($organizer)
        ->get(route('organizer.events.create'))
        ->assertOk()
        ->assertSee('<html', false)
        ->assertSee('</html>', false);
});
