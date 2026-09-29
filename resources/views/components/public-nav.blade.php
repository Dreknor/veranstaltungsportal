{{--
    Öffentliche Hauptnavigation (Startseite und öffentliche Seiten).
    Ab md: Links nebeneinander, darunter: Menü-Button mit ausklappbarem Menü.
--}}
@props(['sticky' => false])

@php
    $isOrganizer = auth()->check() && auth()->user()->hasRole('organizer');
    $links = [
        ['label' => 'Veranstaltungen', 'url' => route('events.index'), 'active' => request()->routeIs('events.index', 'events.show')],
        ['label' => 'Kalender', 'url' => route('events.calendar'), 'active' => request()->routeIs('events.calendar')],
    ];
    $linkClass = 'px-3 py-2 rounded-md text-sm font-medium transition';
    $idle = 'text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400';
    $current = 'text-blue-700 dark:text-blue-400';
@endphp

<nav x-data="{ open: false }" @keydown.escape.window="open = false"
     {{ $attributes->class([
        'bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700',
        'sticky top-0 z-50' => $sticky,
     ]) }}
     aria-label="Hauptnavigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-3">
            <a href="/" class="flex items-center gap-2 sm:gap-3 min-w-0">
                <img src="{{ asset('images/logo.png') }}" alt="" class="h-10 w-10 sm:h-14 sm:w-14 object-contain shrink-0">
                <span class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent truncate">
                    {{ config('app.name') }}
                </span>
            </a>

            {{-- Desktop --}}
            <div class="hidden md:flex items-center gap-1">
                @foreach($links as $link)
                    <a href="{{ $link['url'] }}" class="{{ $linkClass }} {{ $link['active'] ? $current : $idle }}"
                       @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
                @endforeach
                @auth
                    @if($isOrganizer)
                        <a href="{{ route('organizer.dashboard') }}" class="{{ $linkClass }} {{ $idle }}">Veranstalter</a>
                    @endif
                    <a href="{{ route('dashboard') }}" class="ml-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg text-sm font-medium hover:shadow-lg transition">
                        Mein Konto
                    </a>
                @else
                    <a href="{{ route('login') }}" class="{{ $linkClass }} {{ $idle }}">Anmelden</a>
                    <a href="{{ route('register') }}" class="ml-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg text-sm font-medium hover:shadow-lg transition">
                        Registrieren
                    </a>
                @endauth
            </div>

            {{-- Mobil: Menü-Button --}}
            <button type="button" @click="open = !open"
                    class="md:hidden inline-flex items-center justify-center h-11 w-11 rounded-lg text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shrink-0"
                    :aria-expanded="open.toString()" aria-controls="public-mobile-menu">
                <span class="sr-only" x-text="open ? 'Menü schließen' : 'Menü öffnen'">Menü öffnen</span>
                <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Mobil: ausklappbares Menü --}}
    <div id="public-mobile-menu" x-show="open" x-cloak x-transition.origin.top
         @click.outside="open = false"
         class="md:hidden border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <div class="px-4 py-3 space-y-1">
            @foreach($links as $link)
                <a href="{{ $link['url'] }}" class="block px-3 py-3 rounded-lg text-base font-medium {{ $link['active'] ? 'bg-blue-50 dark:bg-blue-900/30 ' . $current : $idle . ' hover:bg-gray-50 dark:hover:bg-gray-700' }}"
                   @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
            @auth
                @if($isOrganizer)
                    <a href="{{ route('organizer.dashboard') }}" class="block px-3 py-3 rounded-lg text-base font-medium {{ $idle }} hover:bg-gray-50 dark:hover:bg-gray-700">Veranstalter-Bereich</a>
                @endif
                <a href="{{ route('dashboard') }}" class="block mt-2 px-3 py-3 rounded-lg text-base font-semibold text-center text-white bg-gradient-to-r from-blue-600 to-purple-600">Mein Konto</a>
            @else
                <div class="grid grid-cols-2 gap-2 pt-2">
                    <a href="{{ route('login') }}" class="px-3 py-3 rounded-lg text-base font-medium text-center border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200">Anmelden</a>
                    <a href="{{ route('register') }}" class="px-3 py-3 rounded-lg text-base font-semibold text-center text-white bg-gradient-to-r from-blue-600 to-purple-600">Registrieren</a>
                </div>
            @endauth
        </div>
    </div>
</nav>
