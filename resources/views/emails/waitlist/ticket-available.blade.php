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

Wir haben **{{ $waitlistEntry->quantity }} {{ $waitlistEntry->quantity === 1 ? 'Platz' : 'Plätze' }} für Sie reserviert** – bis **{{ $waitlistEntry->expires_at->format('d.m.Y H:i') }} Uhr**. In dieser Zeit kann niemand sonst diese Plätze buchen.

Bitte nutzen Sie dafür ausschließlich den Button unten – er enthält Ihre persönliche Reservierung. Danach rückt automatisch die nächste Person auf der Warteliste nach.

@component('mail::panel')
**Ihre Anfrage:**
- Anzahl: {{ $waitlistEntry->quantity }} Ticket(s)
@if($waitlistEntry->ticketType)
- Typ: {{ $waitlistEntry->ticketType->name }}
- Preis: {{ number_format($waitlistEntry->ticketType->price * $waitlistEntry->quantity, 2, ',', '.') }} €
@endif
@endcomponent

@component('mail::button', ['url' => $waitlistEntry->claimUrl()])
Reservierte Plätze jetzt buchen
@endcomponent

Bitte handeln Sie zeitnah, um Ihre Buchung zu sichern.

Mit freundlichen Grüßen,<br>
{{ config('app.name') }}

---

<small>
Diese E-Mail wurde automatisch generiert, weil Sie sich für die Warteliste angemeldet haben.
</small>
@endcomponent

