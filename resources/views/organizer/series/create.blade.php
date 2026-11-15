<x-layouts.app title="Neue Veranstaltungsreihe erstellen">
    <x-ui.page-header title="Neue Veranstaltungsreihe erstellen"
        subtitle="Erstellen Sie eine Veranstaltung mit mehreren zusammenhängenden Terminen (z. B. Kursreihen, Weiterbildungen, Workshop-Serien)."
        :back-href="route('organizer.series.index')" back-label="Zurück zu Veranstaltungsreihen" />

    <form method="POST" action="{{ route('organizer.series.store') }}" class="max-w-form">
        @csrf

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <!-- Grundinformationen -->
        <x-ui.section title="Grundinformationen" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Titel der Reihe" name="title" required />
                <x-ui.textarea label="Beschreibung" name="description" optional rows="4" />
                <x-ui.select label="Kategorie" name="event_category_id" :options="$categories->pluck('name', 'id')"
                    placeholder="Bitte wählen" required />
            </div>
        </x-ui.section>

        <!-- Termine der Veranstaltungsreihe -->
        <x-ui.section title="Termine der Veranstaltungsreihe"
            description="Definieren Sie die Termine Ihrer Veranstaltungsreihe. Sie können später weitere Termine hinzufügen oder einzelne Termine bearbeiten."
            class="border-t border-line py-8">
            <x-ui.alert tone="info" class="mb-6">
                <strong>Tipp:</strong> Nach der Erstellung der Reihe können Sie die einzelnen Termine in der Veranstaltungsverwaltung individuell anpassen und weitere Termine hinzufügen.
            </x-ui.alert>

            <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                <x-ui.field label="Anzahl der Termine" name="recurrence_count" type="number" min="1" max="50"
                    :value="5" required hint="Wie viele Termine sollen zunächst erstellt werden?" />
                <x-ui.field label="Standard-Uhrzeit" name="start_time" type="time" :value="'09:00'" required
                    hint="Wird für alle Termine verwendet." />
                <x-ui.field label="Dauer (Minuten)" name="duration" type="number" min="15" max="1440"
                    :value="120" required hint="Dauer jedes Termins." />
            </div>
        </x-ui.section>

        <!-- Veranstaltungsort -->
        <x-ui.section title="Veranstaltungsort" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Veranstaltungsort" name="venue_name" required />
                <x-ui.field label="Adresse" name="venue_address" required />
                <div class="flex flex-wrap gap-x-4 gap-y-5">
                    <x-ui.field label="PLZ" name="venue_postal_code" required class="max-w-40 flex-[1_1_112px]" />
                    <x-ui.field label="Stadt" name="venue_city" required class="min-w-0 flex-[3_1_200px]" />
                </div>
                <x-ui.field label="Land" name="venue_country" :value="'Deutschland'" required />
            </div>
        </x-ui.section>

        <!-- Kostenübersicht -->
        @if (isset($publishingCosts))
            <x-ui.section title="Geschätzte Kosten" class="border-t border-line py-8">
                <x-ui.box tone="zone" padding="sm" class="text-small" id="series-cost-info">
                    @if (count($publishingCosts['breakdown']) > 0)
                        <p class="m-0 mb-3 font-medium">Kosten pro Termin (geschätzt):</p>
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
                        <p class="m-0 mt-4 font-medium">Hochrechnung für die Reihe</p>
                        <p class="m-0 mt-1" id="series-total-estimate">
                            Bei <span id="event-count-display">0</span> Terminen:
                            <strong id="series-total-amount" class="tabular-nums">0,00 €</strong> (geschätzt, netto)
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

        <!-- Optionen -->
        <x-ui.section title="Veröffentlichung" class="border-t border-line py-8">
            <x-ui.checkbox name="is_published" label="Alle Termine sofort veröffentlichen"
                hint="Wenn aktiviert, sind alle generierten Termine sofort für Teilnehmer sichtbar und buchbar." />
        </x-ui.section>

        <!-- Submit -->
        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Veranstaltungsreihe mit Terminen erstellen</x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.series.index')">Abbrechen</x-ui.button>
        </div>
    </form>

    @push('scripts')
        <script>
            // Update series cost estimate based on event count
            const recurrenceCountInput = document.querySelector('input[name="recurrence_count"]');
            const costPerEvent = {{ $publishingCosts['total'] ?? 0 }};

            function updateSeriesCostEstimate() {
                const eventCount = parseInt(recurrenceCountInput?.value || 0);
                const totalCost = eventCount * costPerEvent;

                document.getElementById('event-count-display').textContent = eventCount;
                document.getElementById('series-total-amount').textContent =
                    new Intl.NumberFormat('de-DE', {
                        style: 'currency',
                        currency: 'EUR'
                    }).format(totalCost);
            }

            if (recurrenceCountInput) {
                recurrenceCountInput.addEventListener('input', updateSeriesCostEstimate);
                // Initial update
                updateSeriesCostEstimate();
            }
        </script>
    @endpush
</x-layouts.app>
