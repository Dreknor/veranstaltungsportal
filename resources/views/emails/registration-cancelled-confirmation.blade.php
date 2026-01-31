@component('mail::message')
# Registrierung rückgängig gemacht

Hallo,

Ihre Registrierung wurde erfolgreich rückgängig gemacht. Ihr Konto mit der E-Mail-Adresse **{{ $email }}** wurde vollständig gelöscht.

Falls Sie sich in Zukunft doch registrieren möchten, können Sie dies jederzeit über unsere Website tun.

@component('mail::button', ['url' => route('home')])
Zur Startseite
@endcomponent

Vielen Dank für Ihr Verständnis.

{{ config('app.name') }}
@endcomponent
