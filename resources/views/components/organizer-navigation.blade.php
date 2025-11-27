@if(auth()->check() && (auth()->user()->isOrganizer() || auth()->user()->activeOrganizations()->count() > 0))
    @php($currentOrg = auth()->user()->currentOrganization())
    <div class="bg-white border-b shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <!-- Organization Info -->
                <div class="flex items-center gap-4">
                    @if($currentOrg)
                        <div class="flex items-center gap-3">
                            @if($currentOrg->logo)
                                <img src="{{ asset('storage/'.$currentOrg->logo) }}" class="h-10 w-10 rounded object-cover" alt="{{ $currentOrg->name }}">
                            @else
                                <div class="h-10 w-10 rounded bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-sm">
                                    {{ $currentOrg->initials() }}
                                </div>
                            @endif
                            <div>
                                <div class="font-semibold text-gray-900">{{ $currentOrg->name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst(auth()->user()->getRoleInOrganization($currentOrg)) }}</div>
                            </div>
                        </div>
                    @else
                        <span class="text-gray-500">Keine Organisation ausgewählt</span>
                    @endif
                </div>

                <!-- Navigation Menu -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('organizer.dashboard') }}" class="text-gray-700 hover:text-primary-600 {{ request()->routeIs('organizer.dashboard') ? 'font-semibold text-primary-600' : '' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('organizer.events.index') }}" class="text-gray-700 hover:text-primary-600 {{ request()->routeIs('organizer.events.*') ? 'font-semibold text-primary-600' : '' }}">
                        Events
                    </a>
                    <a href="{{ route('organizer.bookings.index') }}" class="text-gray-700 hover:text-primary-600 {{ request()->routeIs('organizer.bookings.*') ? 'font-semibold text-primary-600' : '' }}">
                        Buchungen
                    </a>
                    <a href="{{ route('organizer.statistics.index') }}" class="text-gray-700 hover:text-primary-600 {{ request()->routeIs('organizer.statistics.*') ? 'font-semibold text-primary-600' : '' }}">
                        Statistiken
                    </a>

                    <!-- Dropdown Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-gray-700 hover:text-primary-600">
                            <span>Mehr</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50">
                            <a href="{{ route('organizer.reviews.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Bewertungen</a>
                            <a href="{{ route('organizer.invoices.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rechnungen</a>
                            <a href="{{ route('organizer.settings.invoice.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rechnungseinstellungen</a>
                            <a href="{{ route('organizer.featured-events.history') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Hervorgehobene Events</a>
                            <div class="border-t border-gray-100"></div>
                            <a href="{{ route('organizer.organization.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Einstellungen</a>
                            <a href="{{ route('organizer.team.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Team</a>
                            <a href="{{ route('organizer.bank-account.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rechnungsdaten</a>
                        </div>
                    </div>
                </nav>

                <!-- Organization Switcher -->
                @include('components.organization-switcher')
            </div>
        </div>
    </div>

    <!-- Mobile Menu (optional) -->
    <div class="md:hidden bg-white border-b" x-data="{ mobileOpen: false }">
        <button @click="mobileOpen = !mobileOpen" class="w-full px-4 py-3 text-left flex items-center justify-between">
            <span class="font-medium">Menü</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <div x-show="mobileOpen" x-cloak class="px-4 pb-4 space-y-2">
            <a href="{{ route('organizer.dashboard') }}" class="block py-2 text-gray-700">Dashboard</a>
            <a href="{{ route('organizer.events.index') }}" class="block py-2 text-gray-700">Events</a>
            <a href="{{ route('organizer.bookings.index') }}" class="block py-2 text-gray-700">Buchungen</a>
            <a href="{{ route('organizer.statistics.index') }}" class="block py-2 text-gray-700">Statistiken</a>
            @if(Route::has('organizer.series.index'))
            <a href="{{ route('organizer.series.index') }}" class="block py-2 text-gray-700">Serien</a>
            @endif
            <a href="{{ route('organizer.reviews.index') }}" class="block py-2 text-gray-700">Bewertungen</a>
            <a href="{{ route('organizer.invoices.index') }}" class="block py-2 text-gray-700">Rechnungen</a>
            <a href="{{ route('organizer.settings.invoice.index') }}" class="block py-2 text-gray-700">Rechnungseinstellungen</a>
            <a href="{{ route('organizer.featured-events.history') }}" class="block py-2 text-gray-700">Hervorgehobene Events</a>
            <a href="{{ route('organizer.organization.edit') }}" class="block py-2 text-gray-700">Einstellungen</a>
            <a href="{{ route('organizer.team.index') }}" class="block py-2 text-gray-700">Team</a>
            <a href="{{ route('organizer.bank-account.index') }}" class="block py-2 text-gray-700">Rechnungsdaten</a>
        </div>
    </div>
@endif
