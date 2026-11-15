{{--
    Seitenleiste des angemeldeten Bereichs (B4): 264 px, weiß, Linie rechts.
    Ab 1024 px steht sie fest links; darunter öffnet der Menü-Button im Kopf sie als <dialog>
    (Fokus bleibt im Menü, Escape schließt, der Fokus kehrt zum Button zurück).
    Die Navigation wird einmal erzeugt und an beiden Stellen ausgegeben.
--}}
@php
    $user = auth()->user();
    $pendingRequests = $user ? $user->getPendingRequestsCount() : 0;
    $unreadNotifications = $user ? $user->unreadNotifications->count() : 0;
    $isOrganizer = $user && $user->hasRole('organizer');
    $isAdmin = $user && $user->hasRole('admin');

    ob_start();
@endphp
<x-ui.sidebar-group>
    <x-layouts.sidebar-link href="{{ route('dashboard') }}" icon="home" :active="request()->routeIs('dashboard')">
        Dashboard
    </x-layouts.sidebar-link>

    <x-layouts.sidebar-link href="{{ route('events.index') }}" icon="calendar" :active="request()->routeIs('events.index', 'events.show')">
        Alle Veranstaltungen
    </x-layouts.sidebar-link>

    <x-layouts.sidebar-link href="{{ route('user.bookings') }}" icon="ticket" :active="request()->routeIs('user.bookings')">
        Meine Buchungen
    </x-layouts.sidebar-link>

    <x-layouts.sidebar-link href="{{ route('favorites.index') }}" icon="heart" :active="request()->routeIs('favorites.index')">
        Favoriten
    </x-layouts.sidebar-link>

    <x-layouts.sidebar-link href="{{ route('badges.index') }}" icon="medal" :active="request()->routeIs('badges.*')">
        Abzeichen
    </x-layouts.sidebar-link>

    <x-layouts.sidebar-link href="{{ route('connections.index') }}" icon="user-group" :active="request()->routeIs('connections.*', 'users.*')"
        :count="$pendingRequests > 9 ? '9+' : $pendingRequests" count-label="offene Anfragen">
        Netzwerk
    </x-layouts.sidebar-link>

    <x-layouts.sidebar-link href="{{ route('notifications.index') }}" icon="bell" :active="request()->routeIs('notifications.*')" :count="$unreadNotifications > 9 ? '9+' : $unreadNotifications"
        count-label="ungelesen">
        Benachrichtigungen
    </x-layouts.sidebar-link>
</x-ui.sidebar-group>

@if ($isOrganizer)
    <x-ui.sidebar-group label="Veranstalter">
        <x-layouts.sidebar-link href="{{ route('organizer.dashboard') }}" icon="chart" :active="request()->routeIs('organizer.dashboard')">
            Veranstalter Dashboard
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('organizer.events.index') }}" icon="calendar" :active="request()->routeIs('organizer.events.*')">
            Meine Veranstaltungen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('organizer.bookings.index') }}" icon="receipt" :active="request()->routeIs('organizer.bookings.*')">
            Buchungen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('organizer.reviews.index') }}" icon="star" :active="request()->routeIs('organizer.reviews.*')">
            Bewertungen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('organizer.invoices.index') }}" icon="document" :active="request()->routeIs('organizer.invoices.*')">
            Rechnungen
        </x-layouts.sidebar-link>

        @if ($user->currentOrganization()?->hasExternalInvoicing())
            <x-layouts.sidebar-link href="{{ route('organizer.billing-data.index') }}" icon="download"
                :active="request()->routeIs('organizer.billing-data.*')">
                Rechnungsdaten
            </x-layouts.sidebar-link>
        @endif

        <x-layouts.sidebar-link href="{{ route('organizer.bank-account.index') }}" icon="bank" :active="request()->routeIs('organizer.bank-account.*')">
            Kontoverbindung
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('organizer.organization.paypal') }}" icon="credit-card"
            :active="request()->routeIs('organizer.organization.paypal*')">
            PayPal-Einstellungen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('organizer.organization.edit') }}" icon="building-office"
            :active="request()->routeIs('organizer.organization.edit')">
            Veranstalter-Profil
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('organizer.team.index') }}" icon="users" :active="request()->routeIs('organizer.team.*')">
            Team-Verwaltung
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>
@endif

@if ($isAdmin)
    <x-ui.sidebar-group label="Administration">
        <x-layouts.sidebar-link href="{{ route('admin.dashboard') }}" icon="chart" :active="request()->routeIs('admin.dashboard')">
            Admin Dashboard
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>

    <x-ui.sidebar-group label="Content-Verwaltung">
        <x-layouts.sidebar-link href="{{ route('admin.users.index') }}" icon="users" :active="request()->routeIs('admin.users.*')">
            Benutzerverwaltung
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.roles.index') }}" icon="shield-check" :active="request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*')">
            Rollen & Rechte
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.organizations.index') }}" icon="building-office"
            :active="request()->routeIs('admin.organizations.*') || request()->routeIs('admin.organizer-fees.*')">
            Organisationen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.events.index') }}" icon="calendar-check" :active="request()->routeIs('admin.events.*')">
            Veranstaltungsverwaltung
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.categories.index') }}" icon="tag" :active="request()->routeIs('admin.categories.*')">
            Kategorien
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.reviews.index') }}" icon="star" :active="request()->routeIs('admin.reviews.*')">
            Bewertungen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.legal-pages.index') }}" icon="scale" :active="request()->routeIs('admin.legal-pages.*')">
            Rechtliche Seiten
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>

    <x-ui.sidebar-group label="Gamification & Social">
        <x-layouts.sidebar-link href="{{ route('admin.badges.index') }}" icon="medal" :active="request()->routeIs('admin.badges.*')">
            Abzeichen-Verwaltung
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.connections.index') }}" icon="user-group" :active="request()->routeIs('admin.connections.*')">
            Verbindungen
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>

    <x-ui.sidebar-group label="Monetarisierung">
        <x-layouts.sidebar-link href="{{ route('admin.featured-events.index') }}" icon="sparkles" :active="request()->routeIs('admin.featured-events.*')">
            Hervorgehobene Veranstaltungen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.invoices.index') }}" icon="document" :active="request()->routeIs('admin.invoices.*')">
            Rechnungen
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.monetization.index') }}" icon="currency" :active="request()->routeIs('admin.monetization.*')">
            Einstellungen
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>

    <x-ui.sidebar-group label="Analytics & Logs">
        <x-layouts.sidebar-link href="{{ route('admin.reporting.index') }}" icon="chart" :active="request()->routeIs('admin.reporting.*')">
            Analytics & Reports
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.audit-logs.index') }}" icon="list" :active="request()->routeIs('admin.audit-logs.*')">
            Audit Logs
        </x-layouts.sidebar-link>

        <x-layouts.sidebar-link href="{{ route('admin.updates.index') }}" icon="download" :active="request()->routeIs('admin.updates.*')">
            Updates
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>

    <x-ui.sidebar-group label="Kommunikation">
        <x-layouts.sidebar-link href="{{ route('admin.newsletter.index') }}" icon="mail" :active="request()->routeIs('admin.newsletter.*')">
            Newsletter
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>
@endif

<x-ui.sidebar-group label="Hilfe & Support">
    <x-layouts.sidebar-link href="{{ route('help.index') }}" icon="help" :active="request()->routeIs('help.*')">
        Hilfe & Anleitungen
    </x-layouts.sidebar-link>
</x-ui.sidebar-group>

@auth
    <x-ui.sidebar-group label="Datenschutz">
        <x-layouts.sidebar-link href="{{ route('data-privacy.index') }}" icon="shield-check" :active="request()->routeIs('data-privacy.*')">
            Meine Daten & DSGVO
        </x-layouts.sidebar-link>
    </x-ui.sidebar-group>
@endauth
@php
    $navigation = ob_get_clean();
@endphp

{{-- ab 1024 px: feste Seitenleiste --}}
<aside class="sticky top-0 hidden h-screen w-[264px] flex-none flex-col border-r border-line bg-surface lg:flex">
    <div class="flex h-16 flex-none items-center border-b border-line px-6">
        <x-wordmark />
    </div>
    <nav aria-label="Bereichsnavigation" class="flex-1 overflow-y-auto px-3 pt-4 pb-8">
        {!! $navigation !!}
    </nav>
</aside>

{{-- unter 1024 px: einblendbares Menü --}}
<dialog id="app-sidebar" aria-label="Menü" x-data x-on:open-sidebar.window="if (! $el.open) $el.showModal()"
    x-on:click.self="$el.close()"
    x-on:resize.window.debounce.100ms="if (window.innerWidth >= 1024 && $el.open) $el.close()"
    class="m-0 h-full max-h-none w-[min(320px,calc(100%-48px))] max-w-none border-0 border-r border-line bg-surface p-0 text-ink backdrop:bg-ink/50 lg:hidden">
    <div class="flex h-full flex-col">
        <div class="flex h-16 flex-none items-center justify-between gap-2 border-b border-line pr-2 pl-4">
            <x-wordmark />
            <button type="button" autofocus x-on:click="$el.closest('dialog').close()" aria-label="Menü schließen"
                class="inline-flex h-11 w-11 flex-none cursor-pointer items-center justify-center rounded-control border-0 bg-transparent text-ink transition-colors duration-150 hover:bg-zone">
                <x-icon.x class="h-5 w-5" />
            </button>
        </div>
        <nav aria-label="Bereichsnavigation" class="flex-1 overflow-y-auto px-3 pt-4 pb-8">
            {!! $navigation !!}
        </nav>
    </div>
</dialog>
