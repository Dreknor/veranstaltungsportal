@component('mail::message')
# Wartelisten-Bestätigung

Hallo {{ $waitlistEntry->name }},

Sie wurden erfolgreich zur Warteliste für die folgende Veranstaltung hinzugefügt:

**{{ $waitlistEntry->event->title }}**

@if($waitlistEntry->event->start_date)
**Termin:** {{ $waitlistEntry->event->start_date->format('d.m.Y H:i') }} Uhr
@endif

@if($waitlistEntry->event->location)
**Ort:** {{ $waitlistEntry->event->location }}
@endif

## Ihre Anfrage

- **Anzahl Tickets:** {{ $waitlistEntry->quantity }}
@if($waitlistEntry->ticketType)
- **Ticket-Typ:** {{ $waitlistEntry->ticketType->name }} ({{ number_format($waitlistEntry->ticketType->price, 2, ',', '.') }} €)
@endif

Wir benachrichtigen Sie per E-Mail, sobald Tickets verfügbar werden. Sie haben dann **48 Stunden** Zeit, um Ihre Buchung abzuschließen.

@component('mail::button', ['url' => route('events.show', $waitlistEntry->event->slug)])
Veranstaltung ansehen
@endcomponent

Mit freundlichen Grüßen,<br>
{{ config('app.name') }}

---

<small>
Sie möchten sich von der Warteliste abmelden? Besuchen Sie die Veranstaltungsseite und klicken Sie auf „Von Warteliste entfernen“.
</small>
@endcomponent

