<?php

use App\Models\Organization;

test('Rollen der Organisation haben deutsche Anzeigenamen', function () {
    expect(Organization::roleLabel('owner'))->toBe('Inhaber')
        ->and(Organization::roleLabel('admin'))->toBe('Administrator')
        ->and(Organization::roleLabel('member'))->toBe('Mitglied')
        ->and(Organization::roleLabel('gast'))->toBe('gast')
        ->and(Organization::roleLabel(null))->toBe('');
});

test('Pluralformen im Veranstalterbereich', function () {
    expect(trans_choice('{1} :count Buchung|[0,*] :count Buchungen', 1))->toBe('1 Buchung')
        ->and(trans_choice('{1} :count Buchung|[0,*] :count Buchungen', 0))->toBe('0 Buchungen')
        ->and(trans_choice('{1} :count Buchung|[0,*] :count Buchungen', 3))->toBe('3 Buchungen');
});
