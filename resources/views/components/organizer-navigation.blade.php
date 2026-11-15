{{--
    Leiste des Veranstalter-Bereichs unter dem Kopf (nur auf organizer.*-Routen): aktuelle Organisation
    und die wichtigsten Bereiche. Ab 768 px als Linkzeile mit Menü „Mehr", darunter aufklappbar.
    Der Organisationswechsler sitzt im Kopf (B4).
--}}
@if (auth()->check() && (auth()->user()->isOrganizer() || auth()->user()->activeOrganizations()->count() > 0))
    @php($currentOrg = auth()->user()->currentOrganization())
    <div class="border-b border-line bg-surface" x-data="{ mobileOpen: false }">
        <div class="mx-auto flex max-w-page flex-wrap items-center gap-x-6 gap-y-2 px-page py-2 lg:px-8">
            <div class="flex min-w-0 items-center gap-3">
                @if ($currentOrg)
                    @if ($currentOrg->logo)
                        <img src="{{ asset('storage/' . $currentOrg->logo) }}"
                            class="h-9 w-9 flex-none rounded-control border border-line object-cover" alt="">
                    @else
                        <span aria-hidden="true"
                            class="inline-flex h-9 w-9 flex-none items-center justify-center rounded-control border border-line bg-zone text-small font-semibold text-ink">
                            {{ $currentOrg->initials() }}
                        </span>
                    @endif
                    <div class="min-w-0">
                        <p class="m-0 truncate text-ui font-semibold text-ink">{{ $currentOrg->name }}</p>
                        <p class="m-0 text-caption text-muted">
                            {{ ucfirst(auth()->user()->getRoleInOrganization($currentOrg)) }}</p>
                    </div>
                @else
                    <p class="m-0 text-ui text-muted">Keine Organisation ausgewählt</p>
                @endif
            </div>

            <nav aria-label="Veranstalter-Bereich" class="ml-auto hidden items-center gap-1 md:flex">
                <x-ui.nav-link :href="route('organizer.dashboard')" :active="request()->routeIs('organizer.dashboard')">
                    Dashboard
                </x-ui.nav-link>
                <x-ui.nav-link :href="route('organizer.events.index')" :active="request()->routeIs('organizer.events.*')">
                    Veranstaltungen
                </x-ui.nav-link>
                <x-ui.nav-link :href="route('organizer.bookings.index')" :active="request()->routeIs('organizer.bookings.*')">
                    Buchungen
                </x-ui.nav-link>
                <x-ui.nav-link :href="route('organizer.statistics.index')" :active="request()->routeIs('organizer.statistics.*')">
                    Statistiken
                </x-ui.nav-link>

                <x-ui.dropdown width="w-64">
                    <x-slot:trigger>
                        <button type="button" x-ref="button" x-on:click="toggle()"
                            x-bind:aria-expanded="open.toString()" aria-expanded="false"
                            class="inline-flex min-h-11 cursor-pointer items-center gap-1 border-0 bg-transparent px-3 text-ui font-medium text-ink underline-offset-[3px] hover:underline">
                            Mehr
                            <x-icon.chevron-down class="h-4 w-4 transition-transform duration-150"
                                x-bind:class="open && 'rotate-180'" />
                        </button>
                    </x-slot:trigger>
                    <x-ui.dropdown-item :href="route('organizer.reviews.index')">Bewertungen</x-ui.dropdown-item>
                    <x-ui.dropdown-item :href="route('organizer.invoices.index')">Rechnungen</x-ui.dropdown-item>
                    <x-ui.dropdown-item :href="route('organizer.settings.invoice.index')">Rechnungseinstellungen</x-ui.dropdown-item>
                    <x-ui.dropdown-item :href="route('organizer.featured-events.history')">Hervorgehobene Veranstaltungen</x-ui.dropdown-item>
                    <div class="my-1 border-t border-line"></div>
                    <x-ui.dropdown-item :href="route('organizer.organization.edit')">Einstellungen</x-ui.dropdown-item>
                    <x-ui.dropdown-item :href="route('organizer.team.index')">Team</x-ui.dropdown-item>
                    <x-ui.dropdown-item :href="route('organizer.bank-account.index')">Rechnungsdaten</x-ui.dropdown-item>
                </x-ui.dropdown>
            </nav>

            <button type="button" x-on:click="mobileOpen = !mobileOpen" x-bind:aria-expanded="mobileOpen.toString()"
                aria-expanded="false" aria-controls="organizer-mobile-menu"
                class="ml-auto inline-flex min-h-11 cursor-pointer items-center gap-2 rounded-control border border-line-strong bg-surface px-3 text-ui font-medium text-ink transition-colors duration-150 hover:border-ink hover:bg-zone md:hidden">
                Menü
                <x-icon.chevron-down class="h-4 w-4 transition-transform duration-150"
                    x-bind:class="mobileOpen && 'rotate-180'" />
            </button>
        </div>

        <nav id="organizer-mobile-menu" aria-label="Veranstalter-Bereich" x-show="mobileOpen" x-cloak
            class="border-t border-line px-page py-2 md:hidden">
            <x-ui.nav-link block :href="route('organizer.dashboard')" :active="request()->routeIs('organizer.dashboard')">Dashboard</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.events.index')" :active="request()->routeIs('organizer.events.*')">Veranstaltungen</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.bookings.index')" :active="request()->routeIs('organizer.bookings.*')">Buchungen</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.statistics.index')" :active="request()->routeIs('organizer.statistics.*')">Statistiken</x-ui.nav-link>
            @if (Route::has('organizer.series.index'))
                <x-ui.nav-link block :href="route('organizer.series.index')" :active="request()->routeIs('organizer.series.*')">Serien</x-ui.nav-link>
            @endif
            <x-ui.nav-link block :href="route('organizer.reviews.index')">Bewertungen</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.invoices.index')">Rechnungen</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.settings.invoice.index')">Rechnungseinstellungen</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.featured-events.history')">Hervorgehobene Veranstaltungen</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.organization.edit')">Einstellungen</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.team.index')">Team</x-ui.nav-link>
            <x-ui.nav-link block :href="route('organizer.bank-account.index')">Rechnungsdaten</x-ui.nav-link>
        </nav>
    </div>
@endif
