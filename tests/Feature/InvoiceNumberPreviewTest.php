<?php

beforeEach(fn () => setupRoles());

it('meldet ein leeres Rechnungsnummern-Format als ungültig statt mit Serverfehler', function () {
    ['organizer' => $organizer] = createOrganizerWithOrganization();

    $this->actingAs($organizer)
        ->postJson(route('organizer.settings.invoice.preview'), ['format' => '', 'padding' => 5])
        ->assertStatus(400)
        ->assertJson(['success' => false]);
});

it('liefert eine Vorschau für ein gültiges Format', function () {
    ['organizer' => $organizer] = createOrganizerWithOrganization();

    $this->actingAs($organizer)
        ->postJson(route('organizer.settings.invoice.preview'), ['format' => 'RE-{YEAR}-{NUMBER}', 'padding' => 5])
        ->assertOk()
        ->assertJson(['success' => true]);
});

it('meldet auch in der Administration ein leeres Format als ungültig', function () {
    $admin = \App\Models\User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->postJson(route('admin.settings.invoice.preview'), ['format' => '', 'padding' => 5])
        ->assertStatus(400);
});
