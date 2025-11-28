{{-- Partial for managing multiple event dates --}}
@if($event->has_multiple_dates)
<div class="bg-white rounded-lg shadow-md p-6 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-900">Termine</h2>
        <button type="button"
                onclick="document.getElementById('add-date-modal').classList.remove('hidden')"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Termin hinzufügen
        </button>
    </div>

    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded mb-4">
        <p class="text-sm text-blue-700">
            <strong>Wichtig:</strong> Teilnehmer buchen immer alle Termine gemeinsam. Die Kapazität ({{ $event->max_attendees ?? 'unbegrenzt' }}) gilt für die gesamte Serie.
        </p>
    </div>

    @if($event->dates->isEmpty())
        <p class="text-gray-500 text-center py-8">Noch keine Termine hinzugefügt.</p>
    @else
        <div class="space-y-3">
            @foreach($event->dates()->orderBy('start_date')->get() as $date)
                <div class="border rounded-lg p-4 {{ $date->is_cancelled ? 'bg-red-50 border-red-200' : 'bg-gray-50' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="flex items-center text-gray-900 font-medium">
                                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    @if($date->start_date->day === $date->end_date->day)
                                        {{ $date->start_date->format('d.m.Y') }}, {{ $date->start_date->format('H:i') }} - {{ $date->end_date->format('H:i') }} Uhr
                                    @else
                                        {{ $date->start_date->format('d.m.Y, H:i') }} - {{ $date->end_date->format('d.m.Y H:i') }} Uhr
                                    @endif
                                </div>
                                @if($date->is_cancelled)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                        ABGESAGT
                                    </span>
                                @endif
                            </div>

                            @if($date->venue_name && $date->venue_name !== $event->venue_name)
                                <div class="flex items-center text-sm text-gray-600 mb-1">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $date->venue_name }}, {{ $date->venue_city }}
                                </div>
                            @endif

                            @if($date->notes)
                                <p class="text-sm text-gray-600 italic mt-2">{{ $date->notes }}</p>
                            @endif

                            @if($date->is_cancelled && $date->cancellation_reason)
                                <div class="mt-2 text-sm text-red-700">
                                    <strong>Absagegrund:</strong> {{ $date->cancellation_reason }}
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 ml-4">
                            @if(!$date->is_cancelled)
                                <button type="button"
                                        onclick="editDate({{ $date->id }})"
                                        class="p-2 text-blue-600 hover:bg-blue-100 rounded transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button type="button"
                                        onclick="cancelDate({{ $date->id }})"
                                        class="p-2 text-orange-600 hover:bg-orange-100 rounded transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @else
                                <form method="POST" action="{{ route('organizer.events.dates.reactivate', [$event->slug, $date]) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="p-2 text-green-600 hover:bg-green-100 rounded transition"
                                            onclick="return confirm('Möchten Sie diesen Termin wirklich reaktivieren?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif

                            @if($event->dates()->count() > 1)
                                <form method="POST" action="{{ route('organizer.events.dates.destroy', [$event->slug, $date]) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-2 text-red-600 hover:bg-red-100 rounded transition"
                                            onclick="return confirm('Möchten Sie diesen Termin wirklich löschen?')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Add Date Modal --}}
<div id="add-date-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Neuer Termin</h3>
                <button type="button" onclick="document.getElementById('add-date-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="add-date-form" method="POST" action="{{ route('organizer.events.dates.store', $event->slug) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Startdatum *</label>
                        <input type="datetime-local" name="start_date" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enddatum *</label>
                        <input type="datetime-local" name="end_date" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 mb-3">Abweichender Veranstaltungsort (optional)</p>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Veranstaltungsort</label>
                            <input type="text" name="venue_name" placeholder="{{ $event->venue_name }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Standard: {{ $event->venue_name ?? 'Nicht angegeben' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" name="venue_address" placeholder="{{ $event->venue_address }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Standard: {{ $event->venue_address ?? 'Nicht angegeben' }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stadt</label>
                                <input type="text" name="venue_city" placeholder="{{ $event->venue_city }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Standard: {{ $event->venue_city ?? 'Nicht angegeben' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PLZ</label>
                                <input type="text" name="venue_postal_code" placeholder="{{ $event->venue_postal_code }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Standard: {{ $event->venue_postal_code ?? 'Nicht angegeben' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hinweise zu diesem Termin</label>
                    <textarea name="notes" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="z.B. 'Dieser Termin findet online statt'"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button"
                            onclick="document.getElementById('add-date-modal').classList.add('hidden')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Termin hinzufügen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Cancel Date Modal --}}
<div id="cancel-date-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Termin absagen</h3>
                <button type="button" onclick="document.getElementById('cancel-date-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="cancel-date-form" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grund für die Absage *</label>
                    <textarea name="cancellation_reason" rows="3" required
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="z.B. 'Dozent erkrankt'"></textarea>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                    <p class="text-sm text-yellow-800">
                        Teilnehmer werden per E-Mail über die Absage informiert.
                    </p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button"
                            onclick="document.getElementById('cancel-date-modal').classList.add('hidden')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Termin absagen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Date Modal --}}
<div id="edit-date-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Termin bearbeiten</h3>
                <button type="button" onclick="closeEditDateModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="edit-date-form" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Startdatum *</label>
                        <input type="datetime-local" id="edit_start_date" name="start_date" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Enddatum *</label>
                        <input type="datetime-local" id="edit_end_date" name="end_date" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 mb-3">Abweichender Veranstaltungsort (optional)</p>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Veranstaltungsort</label>
                            <input type="text" id="edit_venue_name" name="venue_name" placeholder="{{ $event->venue_name }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
                            <input type="text" id="edit_venue_address" name="venue_address" placeholder="{{ $event->venue_address }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stadt</label>
                                <input type="text" id="edit_venue_city" name="venue_city" placeholder="{{ $event->venue_city }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">PLZ</label>
                                <input type="text" id="edit_venue_postal_code" name="venue_postal_code" placeholder="{{ $event->venue_postal_code }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hinweise zu diesem Termin</label>
                    <textarea id="edit_notes" name="notes" rows="3"
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="z.B. 'Dieser Termin findet online statt'"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button"
                            onclick="closeEditDateModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Abbrechen
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Änderungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cancelDate(dateId) {
    const form = document.getElementById('cancel-date-form');
    form.action = '{{ route('organizer.events.dates.cancel', [$event->slug, '__DATE_ID__']) }}'.replace('__DATE_ID__', dateId);
    document.getElementById('cancel-date-modal').classList.remove('hidden');
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
        form.action = '{{ route('organizer.events.dates.update', [$event->slug, '__DATE_ID__']) }}'.replace('__DATE_ID__', dateId);

        // Zeige das Modal
        document.getElementById('edit-date-modal').classList.remove('hidden');
    } catch (error) {
        console.error('Fehler beim Laden der Termin-Daten:', error);
        alert('Fehler beim Laden der Termin-Daten. Bitte laden Sie die Seite neu.');
    }
}

function closeEditDateModal() {
    document.getElementById('edit-date-modal').classList.add('hidden');
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

