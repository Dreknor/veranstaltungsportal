<x-layouts.app title="Newsletter-Verwaltung">
    <x-ui.page-header title="Newsletter-Verwaltung" subtitle="Verwalten und versenden Sie Newsletter an Abonnenten." />

    @if (session('output'))
        <x-ui.alert tone="info" title="Ausgabe des Befehls" class="mb-8">
            <pre class="m-0 overflow-x-auto text-caption">{{ session('output') }}</pre>
        </x-ui.alert>
    @endif

    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Registrierte Abonnenten" :value="number_format($stats['total_subscribers'])" />
        <x-ui.stat label="Gast-Abonnenten" :value="number_format($stats['guest_subscribers'])" />
        <x-ui.stat label="Abonnenten insgesamt" :value="number_format($stats['total_subscribers'] + $stats['guest_subscribers'])"
            sub="Registrierte und Gäste; der Newsletter geht nur an registrierte" />
        <x-ui.stat label="Anteil der Benutzer mit Abonnement" :value="\App\Support\Ui::percent($stats['subscription_rate'])" />
    </div>

    <x-ui.section title="Aktionen" class="mb-12">
        <div class="flex flex-wrap gap-3">
            <x-ui.button :href="route('admin.newsletter.compose', ['type' => 'weekly'])">Wöchentlicher Newsletter</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.newsletter.compose', ['type' => 'monthly'])">Monatlicher Newsletter</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.newsletter.subscribers')">
                <x-slot:icon><x-icon.users /></x-slot:icon>
                Abonnenten-Liste
            </x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.newsletter.export')">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                CSV exportieren
            </x-ui.button>
        </div>
    </x-ui.section>

    <x-ui.section title="Kommende Veranstaltungen" description="Vorschau für den Newsletter.">
        @if ($upcomingEvents->count() > 0)
            <x-ui.table label="Kommende Veranstaltungen">
                <thead>
                    <tr>
                        <th scope="col">Veranstaltung</th>
                        <th scope="col">Beginn</th>
                        <th scope="col" class="max-sm:hidden">Ort</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingEvents as $event)
                        <tr>
                            <th scope="row">{{ $event->title }}</th>
                            <td class="tabular-nums whitespace-nowrap">{{ $event->start_date->format('d.m.Y H:i') }} Uhr
                            </td>
                            <td class="max-sm:hidden">{{ $event->isOnline() ? 'Online' : $event->venue_city }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @else
            <x-ui.empty>Keine kommenden Veranstaltungen verfügbar.</x-ui.empty>
        @endif
    </x-ui.section>
</x-layouts.app>
