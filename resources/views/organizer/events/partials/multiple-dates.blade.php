{{--
    Termine einer Veranstaltung mit mehreren Terminen verwalten (Bearbeitungsseite).
    Liegt außerhalb des Hauptformulars: eigene Formulare für Hinzufügen, Bearbeiten, Absagen,
    Reaktivieren und Löschen. Erwartet: $event. Dialoge über <x-ui.modal> (add-date, edit-date, cancel-date).
--}}
@if ($event->has_multiple_dates)
    <section aria-labelledby="termine-titel" class="border-t border-line py-8">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
            <h2 id="termine-titel" class="m-0 text-h2-form text-ink">Termine</h2>
            <x-ui.button variant="secondary"
                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-date' }))">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Termin hinzufügen
            </x-ui.button>
        </div>

        <x-ui.alert tone="info" class="mb-6">
            <strong class="font-semibold">Wichtig:</strong> Teilnehmer buchen immer alle Termine gemeinsam. Die
            Kapazität ({{ $event->max_attendees ?? 'unbegrenzt' }}) gilt für die gesamte Serie.
        </x-ui.alert>

        @if ($event->dates->isEmpty())
            <x-ui.empty>Noch keine Termine hinzugefügt.</x-ui.empty>
        @else
            <ul class="m-0 list-none divide-y divide-line rounded-box border border-line p-0">
                @foreach ($event->dates()->orderBy('start_date')->get() as $date)
                    <li class="flex flex-col gap-3 p-4">
                        <div class="flex flex-col gap-1">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="inline-flex items-center gap-2 text-ui font-medium text-ink tabular-nums">
                                    <x-icon.calendar class="h-4 w-4 shrink-0 text-muted" />
                                    @if ($date->start_date->isSameDay($date->end_date))
                                        {{ \App\Support\Ui::dateTime($date->start_date, $date->end_date) }}
                                    @else
                                        {{ \App\Support\Ui::date($date->start_date) }},
                                        {{ $date->start_date->format('H:i') }} Uhr
                                        – {{ \App\Support\Ui::date($date->end_date) }},
                                        {{ $date->end_date->format('H:i') }} Uhr
                                    @endif
                                </span>
                                @if ($date->is_cancelled)
                                    <x-ui.status tone="danger">Abgesagt</x-ui.status>
                                @endif
                            </div>

                            @if ($date->venue_name && $date->venue_name !== $event->venue_name)
                                <span class="inline-flex items-center gap-2 text-small text-muted">
                                    <x-icon.location class="h-4 w-4 shrink-0" />
                                    {{ $date->venue_name }}, {{ $date->venue_city }}
                                </span>
                            @endif

                            @if ($date->notes)
                                <p class="m-0 text-small text-muted">{{ $date->notes }}</p>
                            @endif

                            @if ($date->is_cancelled && $date->cancellation_reason)
                                <p class="m-0 text-small text-danger">
                                    <span class="font-medium">Absagegrund:</span> {{ $date->cancellation_reason }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @if (!$date->is_cancelled)
                                <x-ui.button variant="secondary" size="sm"
                                    :onclick="'editDate(' . ($date->id) . ')'">
                                    <x-slot:icon><x-icon.edit /></x-slot:icon>
                                    Bearbeiten
                                </x-ui.button>
                                <x-ui.button variant="secondary" size="sm"
                                    :onclick="'cancelDate(' . ($date->id) . ')'">
                                    <x-slot:icon><x-icon.x /></x-slot:icon>
                                    Absagen
                                </x-ui.button>
                            @else
                                <form method="POST"
                                    action="{{ route('organizer.events.dates.reactivate', [$event->slug, $date]) }}"
                                    class="m-0">
                                    @csrf
                                    <x-ui.button type="submit" variant="secondary" size="sm"
                                        onclick="return confirm('Möchten Sie diesen Termin wirklich reaktivieren?')">
                                        <x-slot:icon><x-icon.check-circle /></x-slot:icon>
                                        Reaktivieren
                                    </x-ui.button>
                                </form>
                            @endif

                            @if ($event->dates()->count() > 1)
                                <form method="POST"
                                    action="{{ route('organizer.events.dates.destroy', [$event->slug, $date]) }}"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="secondary" size="sm"
                                        onclick="return confirm('Möchten Sie diesen Termin wirklich löschen?')">
                                        <x-slot:icon><x-icon.trash /></x-slot:icon>
                                        Löschen
                                    </x-ui.button>
                                </form>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    {{-- Termin hinzufügen --}}
    <x-ui.modal name="add-date" title="Neuer Termin" max-width="640px" id="add-date-modal">
        <form id="add-date-form" method="POST" action="{{ route('organizer.events.dates.store', $event->slug) }}"
            class="m-0 flex flex-col gap-y-5">
            @csrf

            <div class="grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-x-4 gap-y-5">
                <x-ui.field label="Startdatum" name="start_date" id="add_start_date" type="datetime-local" required
                    :old="false" :error="false" />
                <x-ui.field label="Enddatum" name="end_date" id="add_end_date" type="datetime-local" required
                    :old="false" :error="false" />
            </div>

            <fieldset class="m-0 flex min-w-0 flex-col gap-y-5 border-0 border-t border-line p-0 pt-5">
                <legend class="float-left mb-0 w-full p-0 text-small text-muted">Abweichender Veranstaltungsort
                    (optional)</legend>
                <x-ui.field label="Veranstaltungsort" name="venue_name" id="add_venue_name" :old="false"
                    :error="false" :placeholder="($event->venue_name)"
                    :hint="'Standard: ' . ($event->venue_name ?? 'Nicht angegeben')" />
                <x-ui.field label="Adresse" name="venue_address" id="add_venue_address" :old="false"
                    :error="false" :placeholder="($event->venue_address)"
                    :hint="'Standard: ' . ($event->venue_address ?? 'Nicht angegeben')" />
                <div class="flex flex-wrap gap-x-4 gap-y-5">
                    <x-ui.field label="Stadt" name="venue_city" id="add_venue_city" :old="false" :error="false"
                        :placeholder="($event->venue_city)"
                        :hint="'Standard: ' . ($event->venue_city ?? 'Nicht angegeben')"
                        class="min-w-0 flex-[3_1_200px]" />
                    <x-ui.field label="PLZ" name="venue_postal_code" id="add_venue_postal_code" :old="false"
                        :error="false" :placeholder="($event->venue_postal_code)"
                        :hint="'Standard: ' . ($event->venue_postal_code ?? 'Nicht angegeben')"
                        class="min-w-0 flex-[1_1_140px]" />
                </div>
            </fieldset>

            <x-ui.textarea label="Hinweise zu diesem Termin" name="notes" id="add_notes" rows="3" optional
                :old="false" :error="false" placeholder="z. B. 'Dieser Termin findet online statt'" />
        </form>

        <x-slot:footer>
            <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
            <x-ui.button type="submit" form="add-date-form">Termin hinzufügen</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Termin absagen --}}
    <x-ui.modal name="cancel-date" title="Termin absagen" id="cancel-date-modal">
        <form id="cancel-date-form" method="POST" class="m-0 flex flex-col gap-y-5">
            @csrf

            <x-ui.textarea label="Grund für die Absage" name="cancellation_reason" id="cancel_cancellation_reason"
                rows="3" required :old="false" :error="false" placeholder="z. B. 'Dozent erkrankt'" />

            <x-ui.alert tone="warning">Teilnehmer werden per E-Mail über die Absage informiert.</x-ui.alert>
        </form>

        <x-slot:footer>
            <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
            <x-ui.button type="submit" variant="danger" form="cancel-date-form">Termin absagen</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    {{-- Termin bearbeiten --}}
    <x-ui.modal name="edit-date" title="Termin bearbeiten" max-width="640px" id="edit-date-modal">
        <form id="edit-date-form" method="POST" class="m-0 flex flex-col gap-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-[repeat(auto-fit,minmax(220px,1fr))] gap-x-4 gap-y-5">
                <x-ui.field label="Startdatum" name="start_date" id="edit_start_date" type="datetime-local" required
                    :old="false" :error="false" />
                <x-ui.field label="Enddatum" name="end_date" id="edit_end_date" type="datetime-local" required
                    :old="false" :error="false" />
            </div>

            <fieldset class="m-0 flex min-w-0 flex-col gap-y-5 border-0 border-t border-line p-0 pt-5">
                <legend class="float-left mb-0 w-full p-0 text-small text-muted">Abweichender Veranstaltungsort
                    (optional)</legend>
                <x-ui.field label="Veranstaltungsort" name="venue_name" id="edit_venue_name" :old="false"
                    :error="false" :placeholder="($event->venue_name)" />
                <x-ui.field label="Adresse" name="venue_address" id="edit_venue_address" :old="false"
                    :error="false" :placeholder="($event->venue_address)" />
                <div class="flex flex-wrap gap-x-4 gap-y-5">
                    <x-ui.field label="Stadt" name="venue_city" id="edit_venue_city" :old="false"
                        :error="false" :placeholder="($event->venue_city)" class="min-w-0 flex-[3_1_200px]" />
                    <x-ui.field label="PLZ" name="venue_postal_code" id="edit_venue_postal_code"
                        :old="false" :error="false" :placeholder="($event->venue_postal_code)"
                        class="min-w-0 flex-[1_1_140px]" />
                </div>
            </fieldset>

            <x-ui.textarea label="Hinweise zu diesem Termin" name="notes" id="edit_notes" rows="3" optional
                :old="false" :error="false" placeholder="z. B. 'Dieser Termin findet online statt'" />
        </form>

        <x-slot:footer>
            <x-ui.button variant="secondary" onclick="closeEditDateModal()">Abbrechen</x-ui.button>
            <x-ui.button type="submit" form="edit-date-form">Änderungen speichern</x-ui.button>
        </x-slot:footer>
    </x-ui.modal>

    <script>
        function cancelDate(dateId) {
            const form = document.getElementById('cancel-date-form');
            form.action = '{{ route('organizer.events.dates.cancel', [$event->slug, '__DATE_ID__']) }}'.replace(
                '__DATE_ID__',
                dateId);
            window.dispatchEvent(new CustomEvent('open-modal', {
                detail: 'cancel-date'
            }));
        }

        function editDate(dateId) {
            // Lade die Termin-Daten - Sicher mit Try-Catch
            try {
                const datesData = {!! json_encode($event->dates->keyBy('id')) !!};
                const date = datesData[dateId];

                if (!date) {
                    console.error('Termin nicht gefunden:', dateId);
                    alert('Termin konnte nicht geladen werden. Bitte laden Sie die Seite neu.');
                    return;
                }

                // Fülle das Formular mit den aktuellen Daten
                document.getElementById('edit_start_date').value = formatDateTimeForInput(date.start_date);
                document.getElementById('edit_end_date').value = formatDateTimeForInput(date.end_date);
                document.getElementById('edit_venue_name').value = date.venue_name || '';
                document.getElementById('edit_venue_address').value = date.venue_address || '';
                document.getElementById('edit_venue_city').value = date.venue_city || '';
                document.getElementById('edit_venue_postal_code').value = date.venue_postal_code || '';
                document.getElementById('edit_notes').value = date.notes || '';

                // Setze die Form-Action
                const form = document.getElementById('edit-date-form');
                form.action = '{{ route('organizer.events.dates.update', [$event->slug, '__DATE_ID__']) }}'.replace(
                    '__DATE_ID__', dateId);

                // Zeige den Dialog
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: 'edit-date'
                }));
            } catch (error) {
                console.error('Fehler beim Laden der Termin-Daten:', error);
                alert('Fehler beim Laden der Termin-Daten. Bitte laden Sie die Seite neu.');
            }
        }

        function closeEditDateModal() {
            window.dispatchEvent(new CustomEvent('close-modal', {
                detail: 'edit-date'
            }));
        }

        function formatDateTimeForInput(dateString) {
            // Konvertiere das Datum in das datetime-local Format (YYYY-MM-DDTHH:MM)
            const date = new Date(dateString);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');

            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    </script>
@endif
