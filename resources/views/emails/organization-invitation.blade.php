@component('mail::message')
# Einladung zur Organisation

Hallo,

**{{ $invitedBy->fullName() }}** hat Sie zur Organisation **{{ $organization->name }}** eingeladen.

@php
$roleLabels = ['owner' => 'Eigentümer', 'admin' => 'Administrator', 'member' => 'Mitglied'];
@endphp
**Ihre Rolle:** {{ $roleLabels[$role] ?? ucfirst($role) }}

@if($role === 'owner')
Als **Eigentümer** haben Sie volle Kontrolle über die Organisation, einschließlich Team-Verwaltung und Einstellungen.
@elseif($role === 'admin')
Als **Administrator** können Sie Veranstaltungen, Buchungen und die meisten Einstellungen verwalten.
@else
Als **Mitglied** können Sie Veranstaltungen ansehen und Check-Ins durchführen.
@endif

@component('mail::button', ['url' => route('organizer.dashboard')])
Zur Organisation
@endcomponent

@if($organization->description)
**Über die Organisation:**
{{ $organization->description }}
@endif

Mit freundlichen Grüßen,
Ihr {{ config('app.name') }}-Team
@endcomponent

