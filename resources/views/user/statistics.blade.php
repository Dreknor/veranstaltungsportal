<x-layouts.app title="Meine Statistiken">
    <x-ui.page-header title="Meine Fortbildungs-Statistiken" subtitle="Überblick über Ihre Weiterbildungsaktivitäten.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('user.events.past')">Vergangene Veranstaltungen</x-ui.button>
            <x-ui.button :href="route('user.bookings')">Alle Buchungen ansehen</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Buchungen gesamt" :value="$stats['total_bookings']" />
        <x-ui.stat label="Teilgenommen" :value="$stats['total_events_attended']" />
        <x-ui.stat label="Stunden gesamt" :value="\App\Support\Ui::number($stats['total_hours'], 1)" />
        <x-ui.stat label="Investiert" :value="\App\Support\Ui::money($stats['total_spent'])" />
    </div>

    <div class="mb-12 grid gap-x-12 gap-y-12 lg:grid-cols-2">
        <x-ui.section title="Dieses Jahr ({{ now()->year }})">
            <x-ui.dl>
                <x-ui.dl-item label="Buchungen" num>{{ $stats['bookings_this_year'] }}</x-ui.dl-item>
                <x-ui.dl-item label="Teilgenommene Veranstaltungen" num>{{ $stats['events_this_year'] }}</x-ui.dl-item>
                <x-ui.dl-item label="Anstehende Veranstaltungen" num>{{ $stats['upcoming_events'] }}</x-ui.dl-item>
                <x-ui.dl-item label="Abgegebene Bewertungen" num>{{ $stats['reviews_count'] }}</x-ui.dl-item>
            </x-ui.dl>
        </x-ui.section>

        <x-ui.section title="Finanzübersicht">
            <x-ui.dl>
                <x-ui.dl-item label="Gesamt investiert" num>{{ \App\Support\Ui::money($stats['total_spent']) }}</x-ui.dl-item>
                <x-ui.dl-item label="Durchschnitt pro Buchung" num>{{ \App\Support\Ui::money($stats['average_booking_value'] ?? 0) }}</x-ui.dl-item>
                <x-ui.dl-item label="Bestätigte Buchungen" num>{{ $stats['confirmed_bookings'] }}</x-ui.dl-item>
                <x-ui.dl-item label="Stornierte Buchungen" num>{{ $stats['cancelled_bookings'] }}</x-ui.dl-item>
            </x-ui.dl>
        </x-ui.section>
    </div>

    <x-ui.section title="Veranstaltungen nach Kategorie" class="mb-12">
        @if ($stats['events_by_category']->count() > 0)
            @php $categoryTotal = $stats['events_by_category']->sum(); @endphp
            <ul class="m-0 flex list-none flex-col gap-4 p-0">
                @foreach ($stats['events_by_category'] as $category => $count)
                    <li>
                        <div class="mb-1 flex items-baseline justify-between gap-4">
                            <span class="text-small font-medium text-ink">{{ $category ?: 'Ohne Kategorie' }}</span>
                            <span class="text-small text-muted tabular-nums">{{ $count }} {{ $count === 1 ? 'Veranstaltung' : 'Veranstaltungen' }}</span>
                        </div>
                        <x-ui.progress :value="$count" :max="$categoryTotal" decorative />
                    </li>
                @endforeach
            </ul>
        @else
            <x-ui.empty>Noch keine Kategorien vorhanden.</x-ui.empty>
        @endif
    </x-ui.section>

    <x-ui.section title="Buchungen pro Monat ({{ now()->year }})">
        @php $maxBookings = $stats['bookings_by_month']->max() ?: 1; @endphp
        <ol class="m-0 grid h-64 list-none grid-cols-12 gap-1 p-0 sm:gap-2">
            @foreach (range(1, 12) as $month)
                @php
                    $monthKey = str_pad($month, 2, '0', STR_PAD_LEFT);
                    $bookingCount = $stats['bookings_by_month']->get($monthKey, 0);
                    $heightPercent = ($bookingCount / $maxBookings) * 100;
                @endphp
                <li class="flex min-w-0 flex-col gap-1">
                    <div class="relative flex-1">
                        <div class="absolute inset-x-0 bottom-0 flex flex-col" style="height: {{ max($heightPercent, $bookingCount > 0 ? 2 : 0) }}%">
                            <span class="absolute bottom-full w-full pb-1 text-center text-caption text-muted tabular-nums">
                                {{ $bookingCount }}<span class="sr-only"> Buchung{{ $bookingCount != 1 ? 'en' : '' }}</span>
                            </span>
                            <div class="w-full flex-1 rounded-t-control bg-primary"></div>
                        </div>
                    </div>
                    <span class="border-t border-line pt-1 text-center text-caption text-muted">{{ \Carbon\Carbon::create()->month($month)->translatedFormat('M') }}</span>
                </li>
            @endforeach
        </ol>
    </x-ui.section>
</x-layouts.app>
