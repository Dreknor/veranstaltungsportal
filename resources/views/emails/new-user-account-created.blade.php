@component('mail::message')
# Willkommen - Ihr Konto wurde erstellt

Hallo {{ $user->first_name ?? $user->name }},

**{{ $invitedBy->fullName() }}** hat Sie zur Organisation **{{ $organization->name }}** eingeladen und ein Konto für Sie erstellt.

## Ihre Zugangsdaten

**E-Mail:** {{ $user->email }}
**Temporäres Passwort:** `{{ $temporaryPassword }}`

**Ihre Rolle:** {{ ucfirst($role) }}

@if($role === 'owner')
Als **Owner** haben Sie volle Kontrolle über die Organisation, einschließlich Team-Verwaltung und Einstellungen.
@elseif($role === 'admin')
Als **Admin** können Sie Events, Buchungen und die meisten Einstellungen verwalten.
@else
Als **Member** können Sie Events ansehen und Check-Ins durchführen.
@endif

@component('mail::button', ['url' => route('login')])
Jetzt anmelden
@endcomponent

<x-mail::panel>
⚠️ **Wichtig:** Bitte ändern Sie Ihr Passwort nach der ersten Anmeldung in den Profileinstellungen.
</x-mail::panel>

@if($organization->description)
**Über die Organisation:**
{{ $organization->description }}
@endif

---

## Keine Registrierung gewünscht?

Falls Sie diese Registrierung nicht wünschen, können Sie Ihr Konto hier löschen:

@component('mail::button', ['url' => route('user.cancel-registration', $cancellationToken), 'color' => 'red'])
Registrierung rückgängig machen
@endcomponent

<small>Dieser Link ist 7 Tage gültig.</small>

Vielen Dank,
{{ config('app.name') }}
@endcomponent
