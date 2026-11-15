@php
    $pageTitle = config('app.name') . ' - Fort- und Weiterbildungen für Bildungseinrichtungen';
    $pageDescription =
        'Das zentrale Portal für Fort- und Weiterbildungen an evangelischen Schulen. Entdecken Sie Angebote im Rahmen der Aktion Hauptfach Mensch und weitere pädagogische Veranstaltungen.';

    // Nur kommende Veranstaltungen zählen (entspricht der Liste hinter dem Kategorie-Link)
    $categories = \App\Models\EventCategory::where('is_active', true)
        ->withCount(['events as listed_events_count' => fn($q) => $q->listed()])
        ->get();

    $featuredEvents = \App\Models\Event::listed()
        ->featured()
        ->with(['category', 'ticketTypes'])
        ->orderBy('start_date')
        ->limit(3)
        ->get();

    $upcomingEvents = \App\Models\Event::published()
        ->where('start_date', '>=', now())
        ->orderBy('start_date')
        ->with(['category', 'ticketTypes'])
        ->limit(6)
        ->get();

    // Schwerpunkt: Link auf die Kategorie „Hauptfach Mensch", sonst auf die Suche danach
    $focusCategory = $categories->firstWhere('name', 'Hauptfach Mensch');
    $focusHref = $focusCategory
        ? route('events.index', ['category' => $focusCategory->id])
        : route('events.index', ['search' => 'Hauptfach Mensch']);
@endphp

<x-layouts.public :title="$pageTitle" :description="$pageDescription">
    @push('meta')
        <x-meta-tags :title="$pageTitle" :description="$pageDescription" />
    @endpush

    {{-- Einstieg --}}
    <x-ui.zone aria-labelledby="einstieg-titel">
        <div class="flex flex-col gap-8 pt-[clamp(40px,6vw,72px)] pb-[clamp(40px,6vw,64px)]">
            <h1 id="einstieg-titel" class="m-0 max-w-prose text-display text-ink">
                Fortbildungen für Menschen, die Schule gestalten.
            </h1>

            <x-ui.search-bar :action="route('events.index')" class="max-w-[960px]">
                <x-ui.search-cell label="Kategorie" name="category" wide :options="['' => 'Alle'] + $categories->pluck('name', 'id')->all()" />
                <x-ui.search-cell label="Ort" name="city">
                    <input id="city" name="city" type="text" placeholder="Beliebig" autocomplete="address-level2"
                        class="ui-search-control">
                </x-ui.search-cell>
            </x-ui.search-bar>
        </div>
    </x-ui.zone>

    <div class="mx-auto max-w-page px-page pb-[clamp(56px,7vw,96px)]">
        {{-- Empfohlene Veranstaltungen (nur, wenn es welche gibt) --}}
        @if ($featuredEvents->isNotEmpty())
            <x-ui.section title="Empfohlene Veranstaltungen" class="pt-[clamp(40px,5vw,64px)]">
                <x-ui.row-list>
                    @foreach ($featuredEvents as $event)
                        <x-ui.event-row :event="$event" />
                    @endforeach
                </x-ui.row-list>
            </x-ui.section>
        @endif

        {{-- Kommende Veranstaltungen --}}
        <x-ui.section title="Kommende Veranstaltungen" :link-href="route('events.index')" link-label="Alle Veranstaltungen"
            class="pt-[clamp(40px,5vw,64px)]">
            @if ($upcomingEvents->isNotEmpty())
                <x-ui.row-list>
                    @foreach ($upcomingEvents as $event)
                        <x-ui.event-row :event="$event" />
                    @endforeach
                </x-ui.row-list>
            @else
                <x-ui.empty :action-href="route('events.calendar')" action-label="Zum Kalender">
                    Aktuell sind keine kommenden Veranstaltungen geplant.
                </x-ui.empty>
            @endif
        </x-ui.section>

        {{-- Kategorien --}}
        @if ($categories->isNotEmpty())
            <section aria-labelledby="kategorien-titel" class="pt-[clamp(48px,6vw,80px)]">
                <h2 id="kategorien-titel" class="m-0 mb-4 text-h2 text-ink">Kategorien</h2>
                <ul class="m-0 grid list-none grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-8 p-0">
                    @foreach ($categories as $category)
                        <li>
                            <a href="{{ route('events.index', ['category' => $category->id]) }}"
                                class="flex min-h-15 items-center justify-between gap-4 border-t border-line px-2 text-base font-medium text-ink no-underline transition-colors duration-150 hover:bg-zone">
                                <span class="min-w-0">{{ $category->name }}</span>
                                <span class="flex flex-none items-center gap-3 text-small font-normal text-muted tabular-nums">
                                    {{ $category->listed_events_count }}
                                    {{ $category->listed_events_count === 1 ? 'Veranstaltung' : 'Veranstaltungen' }}
                                    <x-icon.arrow-right class="h-4 w-4" />
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Schwerpunkt Hauptfach Mensch --}}
        <section aria-labelledby="schwerpunkt-titel" class="pt-[clamp(48px,6vw,80px)]">
            <x-ui.box tone="zone" padding="lg" class="flex flex-wrap items-start gap-x-16 gap-y-6">
                <div class="flex flex-[1_1_280px] flex-col gap-3">
                    <span class="inline-flex items-center gap-2 text-small font-medium text-ink-soft">
                        <svg class="h-4 w-4 fill-leaf" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M2 14C2 6.5 6.5 2 14 2c0 7.5-4.5 12-12 12z" />
                        </svg>
                        Schwerpunkt
                    </span>
                    <h2 id="schwerpunkt-titel"
                        class="m-0 text-[clamp(26px,3vw,36px)] leading-[1.15] font-semibold tracking-[-0.02em] text-ink">
                        Hauptfach Mensch
                    </h2>
                </div>
                <div class="flex flex-[2_1_400px] flex-col items-start gap-4">
                    <p class="m-0 max-w-form text-h3 leading-[1.55] font-normal tracking-normal text-ink">
                        Jeder Mensch ist einzigartig – mit Stärken und mit Schwächen. Die Themenreihe „Hauptfach
                        Mensch“ stellt diese Haltung in den Mittelpunkt des Schulalltags: Fortbildungen zu Beziehung,
                        Wahrnehmung und Teilhabe, damit Bildung möglichst viele erreicht.
                    </p>
                    <x-ui.button variant="link" size="lg" :href="$focusHref" arrow>Zur Themenreihe</x-ui.button>
                </div>
            </x-ui.box>
        </section>

        {{-- Nutzen --}}
        <section aria-labelledby="nutzen-titel" class="pt-[clamp(48px,6vw,80px)]">
            <h2 id="nutzen-titel" class="m-0 mb-6 text-h2 text-ink">So funktioniert das Portal</h2>
            <ul class="m-0 grid list-none grid-cols-[repeat(auto-fit,minmax(264px,1fr))] gap-x-12 gap-y-8 p-0">
                <li class="flex flex-col gap-3 border-t border-ink pt-6">
                    <x-icon.ticket class="h-6 w-6 text-ink" />
                    <h3 class="m-0 text-h3 text-ink">Buchen in wenigen Schritten</h3>
                    <p class="m-0 max-w-[360px] text-ui font-normal text-muted">
                        Tickets wählen, Daten eintragen, fertig. Ist eine Veranstaltung ausgebucht, setzen Sie sich
                        auf die Warteliste.
                    </p>
                </li>
                <li class="flex flex-col gap-3 border-t border-ink pt-6">
                    <x-icon.document class="h-6 w-6 text-ink" />
                    <h3 class="m-0 text-h3 text-ink">Teilnahmezertifikat nach der Veranstaltung</h3>
                    <p class="m-0 max-w-[360px] text-ui font-normal text-muted">
                        Ihr Zertifikat steht nach der Teilnahme in Ihrem Konto zum Herunterladen bereit.
                    </p>
                </li>
                <li class="flex flex-col gap-3 border-t border-ink pt-6">
                    <x-icon.calendar-check class="h-6 w-6 text-ink" />
                    <h3 class="m-0 text-h3 text-ink">Termine direkt in den eigenen Kalender</h3>
                    <p class="m-0 max-w-[360px] text-ui font-normal text-muted">
                        Gebuchte Termine übernehmen Sie als Kalenderdatei in Outlook, Google Kalender oder Apple
                        Kalender.
                    </p>
                </li>
            </ul>
        </section>

        {{-- Aufruf an Veranstalter --}}
        @if (config('app.allow_organizer_registration', true) || (auth()->check() && auth()->user()->hasRole('organizer')))
            <section aria-labelledby="veranstalter-titel" class="pt-[clamp(48px,6vw,80px)]">
                <x-ui.box padding="none"
                    class="flex flex-wrap items-center justify-between gap-x-12 gap-y-6 px-[clamp(24px,4vw,48px)] py-[clamp(24px,3vw,32px)]">
                    <div class="flex flex-[1_1_320px] flex-col gap-1">
                        <h2 id="veranstalter-titel" class="m-0 text-h2-form text-ink">Sie bieten selbst Fortbildungen an?</h2>
                        <p class="m-0 max-w-[600px] text-base text-muted">
                            Legen Sie Ihre Veranstaltung im Portal an und verwalten Sie Anmeldungen und Warteliste an
                            einem Ort.
                        </p>
                    </div>
                    @guest
                        <x-ui.button variant="secondary" size="lg" :href="route('register')" arrow>
                            Als Veranstalter registrieren
                        </x-ui.button>
                    @else
                        @if (auth()->user()->hasRole('organizer'))
                            <x-ui.button variant="secondary" size="lg" :href="route('organizer.events.create')" arrow>
                                Veranstaltung anlegen
                            </x-ui.button>
                        @else
                            <x-ui.button variant="secondary" size="lg" :href="route('events.index')" arrow>
                                Veranstaltungen entdecken
                            </x-ui.button>
                        @endif
                    @endguest
                </x-ui.box>
            </section>
        @endif
    </div>
</x-layouts.public>
