{{--
    Kopf des angemeldeten Bereichs (B4): 64 px, weiß, Linie unten.
    Unter 1024 px: Menü-Button (öffnet die Seitenleiste) und Wortmarke.
    Ab 1024 px: Seitentitel links. Rechts: Organisationswechsler (Veranstalter-Bereich),
    Benachrichtigungen, Konto.
--}}
@props(['title' => null])

<header class="sticky top-0 z-30 border-b border-line bg-surface">
    <div class="flex h-16 items-center gap-2 px-page">
        <button type="button" x-data x-on:click="$dispatch('open-sidebar')" aria-controls="app-sidebar"
            aria-haspopup="dialog"
            class="-ml-2 inline-flex h-11 w-11 flex-none cursor-pointer items-center justify-center rounded-control border-0 bg-transparent text-ink transition-colors duration-150 hover:bg-zone lg:hidden">
            <x-icon.menu class="h-5 w-5" />
            <span class="sr-only">Menü öffnen</span>
        </button>

        <x-wordmark class="min-w-0 lg:hidden [&>span]:hidden sm:[&>span]:flex" />

        @if ($title)
            <p class="m-0 hidden min-w-0 truncate text-ui font-semibold text-ink lg:block">{{ $title }}</p>
        @endif

        @auth
            <div class="ml-auto flex flex-none items-center gap-1">
                @if (request()->routeIs('organizer.*') &&
                        (auth()->user()->isOrganizer() || auth()->user()->activeOrganizations()->count() > 0))
                    @include('components.organization-switcher')
                @endif

                <x-notification-bell />

                <x-ui.dropdown width="w-64" class="flex-none">
                    <x-slot:trigger>
                        <button type="button" x-ref="button" x-on:click="toggle()"
                            x-bind:aria-expanded="open.toString()" aria-expanded="false"
                            class="inline-flex h-11 cursor-pointer items-center gap-2 rounded-control border-0 bg-transparent px-1.5 text-ink transition-colors duration-150 hover:bg-zone">
                            <img src="{{ Auth::user()->profilePhotoUrl() }}" alt=""
                                class="h-8 w-8 flex-none rounded-full border border-line object-cover">
                            <span class="sr-only">Konto: </span>
                            <span class="hidden max-w-[200px] truncate text-ui font-medium md:inline">{{ Auth::user()->fullName() }}</span>
                            <x-icon.chevron-down class="hidden h-4 w-4 flex-none text-muted md:block" />
                        </button>
                    </x-slot:trigger>

                    <div class="border-b border-line px-4 pt-2 pb-3">
                        <p class="m-0 truncate text-ui font-semibold">{{ Auth::user()->fullName() }}</p>
                        <p class="m-0 truncate text-small text-muted">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="py-1">
                        <x-ui.dropdown-item :href="route('dashboard')">
                            <x-slot:icon><x-icon.home /></x-slot:icon>
                            Dashboard
                        </x-ui.dropdown-item>
                        <x-ui.dropdown-item :href="route('settings.profile.edit')">
                            <x-slot:icon><x-icon.user class="[&_*]:[stroke-width:1.6]" /></x-slot:icon>
                            Profil
                        </x-ui.dropdown-item>
                        <x-ui.dropdown-item :href="route('settings.password.edit')">
                            <x-slot:icon><x-icon.settings /></x-slot:icon>
                            Einstellungen
                        </x-ui.dropdown-item>
                        <x-ui.dropdown-item :href="route('settings.notifications.edit')">
                            <x-slot:icon><x-icon.bell class="[&_*]:[stroke-width:1.6]" /></x-slot:icon>
                            Benachrichtigungen
                        </x-ui.dropdown-item>
                    </div>
                    <div class="border-t border-line pt-1">
                        <x-ui.dropdown-item type="submit" form="logout-form">
                            <x-slot:icon><x-icon.logout /></x-slot:icon>
                            Abmelden
                        </x-ui.dropdown-item>
                    </div>
                </x-ui.dropdown>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        @endauth
    </div>
</header>
