@component('mail::message')
# 🎉 Tickets verfügbar!

Hallo {{ $waitlistEntry->name }},

gute Nachrichten! Für die Veranstaltung, auf deren Warteliste Sie stehen, sind jetzt Tickets verfügbar:

**{{ $waitlistEntry->event->title }}**

@if($waitlistEntry->event->start_date)
📅 {{ $waitlistEntry->event->start_date->format('d.m.Y H:i') }} Uhr
@endif

@if($waitlistEntry->event->location)
📍 {{ $waitlistEntry->event->location }}
@endif

## ⏰ Wichtig: Zeitlich begrenzt!

Sie haben **48 Stunden** Zeit, um Ihre Tickets zu buchen. Diese Reservierung läuft am **{{ $waitlistEntry->expires_at->format('d.m.Y H:i') }} Uhr** ab.

Danach werden die Tickets an die nächsten Personen auf der Warteliste weitergegeben.

@component('mail::panel')
**Ihre Anfrage:**
- Anzahl: {{ $waitlistEntry->quantity }} Ticket(s)
@if($waitlistEntry->ticketType)
- Typ: {{ $waitlistEntry->ticketType->name }}
- Preis: {{ number_format($waitlistEntry->ticketType->price * $waitlistEntry->quantity, 2, ',', '.') }} €
@endif
@endcomponent

@component('mail::button', ['url' => route('bookings.create', $waitlistEntry->event)])
Jetzt buchen
@endcomponent

Beeilen Sie sich - die Tickets sind heiß begehrt!

Mit freundlichen Grüßen,<br>
{{ config('app.name') }}

---

<small>
Diese E-Mail wurde automatisch generiert, weil Sie sich für die Warteliste angemeldet haben.
</small>
@endcomponent

