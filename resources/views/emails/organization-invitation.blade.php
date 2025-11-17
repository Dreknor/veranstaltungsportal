@component('mail::message')
# Einladung zur Organisation

Hallo,

**{{ $invitedBy->fullName() }}** hat Sie zur Organisation **{{ $organization->name }}** eingeladen.

**Ihre Rolle:** {{ ucfirst($role) }}

@if($role === 'owner')
Als **Owner** haben Sie volle Kontrolle über die Organisation, einschließlich Team-Verwaltung und Einstellungen.
@elseif($role === 'admin')
Als **Admin** können Sie Events, Buchungen und die meisten Einstellungen verwalten.
@else
Als **Member** können Sie Events ansehen und Check-Ins durchführen.
@endif

@component('mail::button', ['url' => route('organizer.dashboard')])
Zur Organisation
@endcomponent

@if($organization->description)
**Über die Organisation:**
{{ $organization->description }}
@endif

Vielen Dank,
{{ config('app.name') }}
@endcomponent

