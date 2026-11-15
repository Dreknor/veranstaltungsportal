{{--
    Seitengerüst des angemeldeten Bereichs (B4): Seitenleiste 264 px links (unter 1024 px als
    einblendbares Menü), Kopf 64 px, Inhalt weiß bis 1200 px. Session-Meldungen und die
    Fehlerliste rendert <x-ui.flash> einmal über dem Inhalt.
--}}
@props(['title' => null])
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('components.layouts.app.favicon')
    <title>{{ $title ? $title . ' – ' . config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-surface font-sans text-base text-ink antialiased">
    <a href="#inhalt"
        class="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[60] focus:rounded-control focus:bg-surface focus:px-4 focus:py-3 focus:text-ui focus:font-medium focus:text-ink">Zum
        Inhalt springen</a>

    <div class="flex min-h-screen">
        <x-layouts.app.sidebar />

        <div class="flex min-w-0 flex-1 flex-col">
            <x-layouts.app.header :title="$title" />

            @auth
                @if (request()->routeIs('organizer.*'))
                    @include('components.organizer-navigation')
                @endif
            @endauth

            @if (session()->has('impersonator'))
                <div class="border-b border-warning-line bg-warning-zone text-warning">
                    <div
                        class="mx-auto flex max-w-page flex-wrap items-center justify-between gap-x-6 gap-y-2 px-page py-2">
                        <p class="m-0 flex items-center gap-2 text-small font-medium">
                            <x-icon.warning class="h-5 w-5 flex-none" />
                            Sie verkörpern gerade: {{ auth()->user()->name }}
                        </p>
                        <form action="{{ route('impersonate.leave') }}" method="POST" class="m-0">
                            @csrf
                            <x-ui.button type="submit" variant="secondary" size="sm">Verkörperung
                                beenden</x-ui.button>
                        </form>
                    </div>
                </div>
            @endif

            <main id="inhalt" class="flex-1 bg-surface">
                <div class="mx-auto max-w-page px-page py-8">
                    <x-ui.flash class="mb-6" show-errors />

                    @isset($header)
                        <header class="mb-6 border-b border-line pb-4">
                            {{ $header }}
                        </header>
                    @endisset

                    {{ $slot }}
                </div>
            </main>

            <x-footer compact />
        </div>
    </div>

    @stack('styles')
    @stack('scripts')
</body>

</html>
