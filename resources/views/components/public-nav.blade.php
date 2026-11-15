{{--
    Kopf der öffentlichen Seiten (B4, Referenz startseite.html): 72 px, weiß, Linie unten.
    Links die Wortmarke, rechts „Veranstaltungen", „Kalender" und – je nach Anmeldung – „Anmelden"
    oder „Veranstalter" (44 px, Text ui), dann der Button „Registrieren" bzw. „Mein Konto".
    Unter 768 px rutschen die Links in eine zweite Zeile (kein Menü-Button; es bleiben höchstens drei Links).
--}}
@props(['sticky' => false])

@php
    $isOrganizer = auth()->check() && auth()->user()->hasRole('organizer');
@endphp

<header {{ $attributes->class(['border-b border-line bg-surface', 'sticky top-0 z-50' => $sticky]) }}>
    <div class="mx-auto flex min-h-[72px] max-w-page flex-wrap items-center gap-x-4 px-page">
        <x-wordmark class="mr-auto" />

        <nav aria-label="Hauptnavigation"
            class="order-3 -ml-3 flex w-[calc(100%+12px)] flex-wrap items-center gap-1 border-t border-line md:order-none md:ml-0 md:w-auto md:border-t-0">
            <x-ui.nav-link :href="route('events.index')" :active="request()->routeIs('events.index', 'events.show')">
                Veranstaltungen
            </x-ui.nav-link>
            <x-ui.nav-link :href="route('events.calendar')" :active="request()->routeIs('events.calendar')">
                Kalender
            </x-ui.nav-link>
            @auth
                @if ($isOrganizer)
                    <x-ui.nav-link :href="route('organizer.dashboard')">Veranstalter</x-ui.nav-link>
                @endif
            @else
                <x-ui.nav-link :href="route('login')" :active="request()->routeIs('login')">Anmelden</x-ui.nav-link>
            @endauth
        </nav>

        @auth
            <x-ui.button :href="route('dashboard')">Mein Konto</x-ui.button>
        @else
            <x-ui.button :href="route('register')" :aria-current="request()->routeIs('register') ? 'page' : null">
                Registrieren
            </x-ui.button>
        @endauth
    </div>
</header>
