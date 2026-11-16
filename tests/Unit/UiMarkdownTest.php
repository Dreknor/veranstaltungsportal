<?php

use App\Support\Ui;

test('Markdown entfernt HTML und Skripte', function () {
    $html = Ui::markdown("Hallo <b onclick=\"alert(1)\">Welt</b>\n\n<script>alert('x')</script>\n\n<img src=x onerror=alert(1)>");

    expect($html)->toContain('Hallo')
        ->toContain('Welt')
        ->not->toContain('<script')
        ->not->toContain('onclick')
        ->not->toContain('onerror')
        ->not->toContain('<b');
});

test('Markdown entfernt unsichere Links und kennzeichnet externe Links', function () {
    $html = Ui::markdown('[böse](javascript:alert(1)) [daten](data:text/html;base64,PHNjcmlwdD4=) [extern](https://example.org)');

    expect($html)->not->toContain('javascript:')
        ->not->toContain('data:text')
        ->toContain('href="https://example.org"')
        ->toContain('rel="nofollow noopener noreferrer"');
});

test('Markdown rendert Listen, Fett und Nummerierung', function () {
    $html = Ui::markdown("**Inhalte:**\n- Grundlagen\n- Übungen\n\n1. Erstens\n2. Zweitens");

    expect($html)->toContain('<strong>Inhalte:</strong>')
        ->toContain('<ul>')
        ->toContain('<li>Grundlagen</li>')
        ->toContain('<ol>')
        ->toContain('<li>Zweitens</li>')
        ->not->toContain('**');
});

test('Markdown behält einfache Zeilenumbrüche', function () {
    expect(Ui::markdown("Zeile eins\nZeile zwei"))->toContain("Zeile eins<br>\nZeile zwei");
});

test('Markdown-Überschriften beginnen unter der Abschnittsüberschrift', function () {
    $html = Ui::markdown("# Groß\n## Mittel\n### Klein");

    expect($html)->toContain('<h3>Groß</h3>')
        ->toContain('<h3>Mittel</h3>')
        ->toContain('<h4>Klein</h4>')
        ->not->toContain('<h1')
        ->not->toContain('<h2');
});

test('Markdown zeigt Bilder nur als Alternativtext', function () {
    expect(Ui::markdown('![Lageplan](https://example.org/plan.png)'))
        ->toContain('Lageplan')
        ->not->toContain('<img');
});

test('leere Beschreibung ergibt leeres HTML', function () {
    expect(Ui::markdown(null))->toBe('')
        ->and(Ui::markdown('   '))->toBe('');
});

test('plainText entfernt Markdown für Auszüge', function () {
    $text = "**Inhalte:**\n- Grundlagen\n- Übungen & Praxis";

    expect(Ui::plainText($text))->toBe('Inhalte: Grundlagen Übungen & Praxis')
        ->and(Ui::plainText($text, 12))->toBe('Inhalte: Gru…')
        ->and(Ui::plainText($text, null, true))->toBe("Inhalte:\n\n- Grundlagen\n- Übungen & Praxis");
});

test('Länder werden deutsch angezeigt', function () {
    expect(Ui::country('Germany'))->toBe('Deutschland')
        ->and(Ui::country('DE'))->toBe('Deutschland')
        ->and(Ui::country('Deutschland'))->toBe('Deutschland')
        ->and(Ui::country('Austria'))->toBe('Österreich')
        ->and(Ui::country('Absurdistan'))->toBe('Absurdistan')
        ->and(Ui::country(null))->toBe('');
});

test('Zahlen und Abzeichen-Bezeichnungen sind deutsch', function () {
    expect(Ui::number(1250))->toBe('1.250')
        ->and(Ui::number(856.5, 1))->toBe('856,5')
        ->and(Ui::number(20.0, 1))->toBe('20')
        ->and(Ui::badgeType('attendance'))->toBe('Teilnahme')
        ->and(Ui::badgeType('achievement'))->toBe('Erfolg')
        ->and(Ui::badgeType('special'))->toBe('Spezial')
        ->and(Ui::badgeRequirement('total_hours_attended'))->toBe('Fortbildungsstunden')
        ->and(Ui::badgeRequirement('bookings_made'))->toBe('Bezahlte Buchungen');
});

test('Benachrichtigungen bekommen deutsche Titel und Anführungszeichen', function () {
    expect(Ui::notificationTitle('App\Notifications\EventReminderNotification', ['title' => 'Event-Erinnerung']))
        ->toBe('Erinnerung an Ihre Veranstaltung')
        ->and(Ui::notificationTitle('App\Notifications\BadgeEarnedNotification', ['message' => 'x']))
        ->toBe('Neues Abzeichen')
        ->and(Ui::notificationTitle('App\Notifications\Unbekannt', []))->toBe('Benachrichtigung')
        ->and(Ui::notificationMessage(['message' => 'Ihre Buchung für "Klassenrat" wurde bestätigt.']))
        ->toBe('Ihre Buchung für „Klassenrat“ wurde bestätigt.')
        ->and(Ui::notificationMessage(['message' => 'Anna hat 1 Ticket(s) gebucht, Ben 3 Ticket(s)']))
        ->toBe('Anna hat 1 Ticket gebucht, Ben 3 Tickets');
});
