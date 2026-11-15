<x-layouts.app title="Veranstaltungsreihe bearbeiten">
    <x-ui.page-header title="Veranstaltungsreihe bearbeiten" :subtitle="$series->title"
        :back-href="route('organizer.series.show', $series)" back-label="Zurück zu Reihendetails" />

    <form method="POST" action="{{ route('organizer.series.update', $series) }}" class="max-w-form">
        @csrf
        @method('PUT')

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grundinformationen" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Titel der Veranstaltungsreihe" name="title" :value="$series->title" required
                    hint="Dieser Titel wird für alle Termine der Reihe verwendet." />
                <x-ui.textarea label="Beschreibung" name="description" :value="$series->description" optional rows="4"
                    hint="Beschreibung, die für alle Termine der Reihe gilt." />
            </div>
        </x-ui.section>

        <x-ui.section title="Status" class="border-t border-line py-8">
            <x-ui.checkbox name="is_active" :checked="(bool) $series->is_active" label="Reihe ist aktiv"
                hint="Wenn deaktiviert, werden keine neuen Buchungen für Termine dieser Reihe mehr akzeptiert." />
        </x-ui.section>

        <!-- Kostenübersicht -->
        @if (isset($publishingCosts))
            <x-ui.section title="Geschätzte Kosten pro Termin" class="border-t border-line py-8">
                <x-ui.box tone="zone" padding="sm" class="text-small">
                    @if (count($publishingCosts['breakdown']) > 0)
                        <p class="m-0 mb-3 font-medium">Kosten pro Termin der Reihe (geschätzt):</p>
                        <div class="flex flex-col gap-2">
                            @foreach ($publishingCosts['breakdown'] as $item)
                                <div class="flex justify-between gap-4">
                                    <span>{{ $item['label'] }}</span>
                                    <span class="font-medium tabular-nums">{{ number_format($item['amount'], 2, ',', '.') }} €</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between gap-4 border-t border-line pt-2 font-semibold">
                                <span>Pro Termin (netto):</span>
                                <span class="tabular-nums">{{ number_format($publishingCosts['total'], 2, ',', '.') }} €</span>
                            </div>
                        </div>
                        <p class="m-0 mt-4 font-medium">Hochrechnung für diese Reihe</p>
                        <p class="m-0 mt-1">
                            Bei {{ $series->events->count() }} Terminen:
                            <strong class="tabular-nums">{{ number_format($series->events->count() * $publishingCosts['total'], 2, ',', '.') }} €</strong>
                            (geschätzt, netto)
                        </p>
                        <p class="m-0 mt-1 text-muted">
                            Die tatsächliche Abrechnung erfolgt nach Ende jedes einzelnen Termins basierend auf den realen Buchungen.
                        </p>
                    @else
                        <p class="m-0 mb-3 font-medium">Ihre Plattformgebühren:</p>
                        @if (isset($platformFeeInfo))
                            <p class="m-0 font-semibold">{{ $platformFeeInfo['description'] }}</p>
                            <p class="m-0 mt-1 text-muted">
                                @if ($platformFeeInfo['type'] === 'percentage')
                                    Bei einem Ticket-Umsatz von 100 € fallen {{ number_format($platformFeeInfo['percentage'], 2) }} € Gebühr an.
                                @else
                                    Pro verkauftem Ticket/Buchung: {{ number_format($platformFeeInfo['fixed_amount'], 2, ',', '.') }} €
                                @endif
                            </p>
                        @endif
                        <p class="m-0 mt-3">Die genaue Abrechnung erfolgt nach Ende der Veranstaltung pro Termin basierend auf den tatsächlichen Buchungen.</p>
                        <p class="m-0 mt-3 text-muted">
                            <strong>Hinweis:</strong> Die finale Rechnung hängt von den tatsächlichen Buchungen ab.
                            Jeder Termin wird einzeln nach Ende der Veranstaltung abgerechnet.
                        </p>
                    @endif
                </x-ui.box>
            </x-ui.section>
        @endif

        <x-ui.alert tone="info" class="mb-8">
            <strong>Hinweis:</strong> Änderungen am Titel und der Beschreibung werden NICHT automatisch auf bereits erstellte Termine übertragen.
            Um einzelne Termine zu bearbeiten, nutzen Sie die Veranstaltungsverwaltung.
        </x-ui.alert>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Änderungen speichern</x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.series.show', $series)">Abbrechen</x-ui.button>
        </div>
    </form>
</x-layouts.app>
