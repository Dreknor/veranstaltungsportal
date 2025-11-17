@component('mail::message')
# Ihre Rolle wurde geändert

Hallo,

Ihre Rolle in der Organisation **{{ $organization->name }}** wurde geändert.

**Vorherige Rolle:** {{ ucfirst($oldRole) }}
**Neue Rolle:** {{ ucfirst($newRole) }}

@if($newRole === 'owner')
Sie haben jetzt volle Kontrolle über die Organisation.
@elseif($newRole === 'admin')
Sie können jetzt Events, Buchungen und die meisten Einstellungen verwalten.
@else
Sie haben jetzt eingeschränkte Berechtigungen (Events ansehen und Check-Ins).
@endif

Diese Änderung wurde von **{{ $changedBy->fullName() }}** durchgeführt.

@component('mail::button', ['url' => route('organizer.dashboard')])
Zum Dashboard
@endcomponent

Vielen Dank,
{{ config('app.name') }}
@endcomponent

