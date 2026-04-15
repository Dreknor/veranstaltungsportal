@component('mail::message')
# Ihre Rolle wurde geändert

Hallo,

Ihre Rolle in der Organisation **{{ $organization->name }}** wurde geändert.

@php
$roleLabels = ['owner' => 'Eigentümer', 'admin' => 'Administrator', 'member' => 'Mitglied'];
@endphp
**Vorherige Rolle:** {{ $roleLabels[$oldRole] ?? ucfirst($oldRole) }}
**Neue Rolle:** {{ $roleLabels[$newRole] ?? ucfirst($newRole) }}

@if($newRole === 'owner')
Sie haben jetzt als **Eigentümer** volle Kontrolle über die Organisation.
@elseif($newRole === 'admin')
Als **Administrator** können Sie jetzt Veranstaltungen, Buchungen und die meisten Einstellungen verwalten.
@else
Als **Mitglied** haben Sie jetzt eingeschränkte Berechtigungen (Veranstaltungen ansehen und Check-Ins).
@endif

Diese Änderung wurde von **{{ $changedBy->fullName() }}** durchgeführt.

@component('mail::button', ['url' => route('organizer.dashboard')])
Zum Dashboard
@endcomponent

Mit freundlichen Grüßen,
Ihr {{ config('app.name') }}-Team
@endcomponent

