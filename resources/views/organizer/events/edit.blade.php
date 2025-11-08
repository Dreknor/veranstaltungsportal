<x-layouts.app title="Event bearbeiten">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('organizer.events.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    ← Zurück zur Übersicht
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Event bearbeiten</h1>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('organizer.events.update', $event) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Grunddaten -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Grunddaten</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                            <input type="text" id="title" name="title" required value="{{ old('title', $event->title) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="event_category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategorie *</label>
                            <select id="event_category_id" name="event_category_id" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('event_category_id', $event->event_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung *</label>
                            <textarea id="description" name="description" required rows="6"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $event->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Datum und Uhrzeit -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Datum und Uhrzeit</h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Startdatum *</label>
                            <input type="datetime-local" id="start_date" name="start_date" required
                                   value="{{ old('start_date', $event->start_date->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Enddatum *</label>
                            <input type="datetime-local" id="end_date" name="end_date" required
                                   value="{{ old('end_date', $event->end_date->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Veranstaltungsort -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Veranstaltungsort</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="venue_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" id="venue_name" name="venue_name" required
                                   value="{{ old('venue_name', $event->venue_name) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="venue_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse *</label>
                            <input type="text" id="venue_address" name="venue_address" required
                                   value="{{ old('venue_address', $event->venue_address) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label for="venue_city" class="block text-sm font-medium text-gray-700 mb-1">Stadt *</label>
                                <input type="text" id="venue_city" name="venue_city" required
                                       value="{{ old('venue_city', $event->venue_city) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('venue_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="venue_postal_code" class="block text-sm font-medium text-gray-700 mb-1">PLZ *</label>
                                <input type="text" id="venue_postal_code" name="venue_postal_code" required
                                       value="{{ old('venue_postal_code', $event->venue_postal_code) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('venue_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="venue_country" class="block text-sm font-medium text-gray-700 mb-1">Land *</label>
                            <input type="text" id="venue_country" name="venue_country" required
                                   value="{{ old('venue_country', $event->venue_country) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Einstellungen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Einstellungen</h2>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="is_published" name="is_published" value="1"
                                   {{ old('is_published', $event->is_published) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label for="is_published" class="ml-2 text-sm text-gray-700">Event veröffentlichen</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                   {{ old('is_featured', $event->is_featured) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label for="is_featured" class="ml-2 text-sm text-gray-700">Als Featured markieren</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="is_private" name="is_private" value="1"
                                   {{ old('is_private', $event->is_private) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label for="is_private" class="ml-2 text-sm text-gray-700">Privates Event (Access Code erforderlich)</label>
                        </div>

                        <div id="access-code-field" class="{{ old('is_private', $event->is_private) ? '' : 'hidden' }}">
                            <label for="access_code" class="block text-sm font-medium text-gray-700 mb-1">Access Code</label>
                            <input type="text" id="access_code" name="access_code"
                                   value="{{ old('access_code', $event->access_code) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('access_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('organizer.events.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Abbrechen
                    </a>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Event aktualisieren
                    </button>
                </div>
            </form>

            <!-- Ticket-Typen (außerhalb des Hauptformulars) -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Ticket-Typen</h2>

                @if($ticketTypes->isEmpty())
                    <p class="text-gray-600 mb-4">Noch keine Ticket-Typen definiert.</p>
                @else
                    <div class="space-y-4 mb-4">
                        @foreach($ticketTypes as $ticketType)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $ticketType->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $ticketType->description }}</p>
                                        <div class="mt-2 text-sm text-gray-500">
                                            Preis: {{ number_format($ticketType->price, 2, ',', '.') }} € |
                                            Verfügbar: {{ $ticketType->availableQuantity() }} von {{ $ticketType->quantity ?? '∞' }} |
                                            Verkauft: {{ $ticketType->quantity_sold }}
                                        </div>
                                    </div>
                                    <form action="{{ route('organizer.events.ticket-types.destroy', [$event, $ticketType]) }}" method="POST" onsubmit="return confirm('Wirklich löschen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('organizer.events.ticket-types.create', $event) }}"
                   class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    Ticket-Typ hinzufügen
                </a>

                <button type="button" onclick="document.getElementById('add-ticket-form').classList.toggle('hidden')"
                        class="ml-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Schnell hinzufügen
                </button>

                <div id="add-ticket-form" class="hidden mt-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <h3 class="font-semibold mb-4 text-gray-900">Neuen Ticket-Typ hinzufügen</h3>

                    <form action="{{ route('organizer.events.ticket-types.store', $event) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="redirect_to_edit" value="1">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="ticket_name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" id="ticket_name" name="name" required
                                       placeholder="z.B. Standard, VIP, Ermäßigt"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="ticket_price" class="block text-sm font-medium text-gray-700 mb-1">Preis (€) *</label>
                                <input type="number" id="ticket_price" name="price" required min="0" step="0.01"
                                       placeholder="0.00"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="ticket_description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                            <textarea id="ticket_description" name="description" rows="2"
                                      placeholder="Optional: Beschreibung des Ticket-Typs"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="ticket_quantity" class="block text-sm font-medium text-gray-700 mb-1">Verfügbare Anzahl</label>
                                <input type="number" id="ticket_quantity" name="quantity" min="1"
                                       placeholder="Leer = unbegrenzt"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Leer lassen für unbegrenzte Tickets</p>
                            </div>

                            <div>
                                <label for="ticket_max_per_order" class="block text-sm font-medium text-gray-700 mb-1">Max. pro Buchung</label>
                                <input type="number" id="ticket_max_per_order" name="max_per_order" min="1"
                                       placeholder="z.B. 10"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-xs text-gray-500 mt-1">Maximal buchbare Anzahl</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="ticket_is_available" name="is_available" value="1" checked
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <label for="ticket_is_available" class="ml-2 text-sm text-gray-700">Ticket-Typ aktivieren</label>
                        </div>

                        <div class="flex justify-end space-x-2 pt-2 border-t">
                            <button type="button" onclick="document.getElementById('add-ticket-form').classList.add('hidden')"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                                Abbrechen
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Ticket-Typ speichern
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('is_private').addEventListener('change', function() {
            document.getElementById('access-code-field').classList.toggle('hidden', !this.checked);
        });
    </script>
</x-layouts.app>

