{{--
    Öffentliches Seitengerüst (B4): öffentliche Seiten, Anmeldung, Buchung, Rechtliches.
    Kopf <x-public-nav>, Session-Meldungen einmal über dem Inhalt, Fuß <x-footer>.
    Seiten geben ihren Inhalt selbst auf die Seitenbreite (max-w-page px-page).
--}}
@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.app.favicon')
    <title>{{ $title ?? config('app.name') }}</title>
    <meta name="description" content="{{ $description ?? 'Veranstaltungsportal für Fort- und Weiterbildungen' }}">
    @stack('meta')

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (config('recaptcha.enabled') && config('recaptcha.site_key'))
        <meta name="recaptcha-site-key" content="{{ config('recaptcha.site_key') }}">
    @endif
</head>

<body class="flex min-h-screen flex-col bg-surface font-sans text-base text-ink antialiased">
    <a href="#inhalt"
        class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[60] focus:rounded-control focus:bg-surface focus:px-4 focus:py-3 focus:text-ui focus:font-medium focus:text-ink">Zum
        Inhalt springen</a>

    <x-public-nav />

    <main id="inhalt" class="flex-1">
        <x-ui.flash class="mx-auto mt-6 mb-6 max-w-page px-page" />
        {{ $slot }}
    </main>

    <x-footer />

    @stack('scripts')
</body>

</html>
