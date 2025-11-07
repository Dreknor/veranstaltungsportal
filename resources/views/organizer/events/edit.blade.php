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

                <!-- Ticket-Typen -->
                <div class="bg-white rounded-lg shadow-md p-6">
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
                                        <form action="{{ route('organizer.events.tickets.delete', [$event, $ticketType]) }}" method="POST" onsubmit="return confirm('Wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <button type="button" onclick="document.getElementById('add-ticket-form').classList.toggle('hidden')"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Ticket-Typ hinzufügen
                    </button>

                    <div id="add-ticket-form" class="hidden mt-4 p-4 border border-gray-200 rounded-lg">
                        <h3 class="font-semibold mb-4">Neuer Ticket-Typ</h3>
                        <!-- Hier würde ein separates Formular für Ticket-Typen kommen -->
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
        </div>
    </div>

    <script>
        document.getElementById('is_private').addEventListener('change', function() {
            document.getElementById('access-code-field').classList.toggle('hidden', !this.checked);
        });
    </script>
</x-layouts.app>
<x-layouts.app title="Meine Events">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Meine Events</h1>
                <a href="{{ route('organizer.events.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    Neues Event erstellen
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($events->isEmpty())
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <p class="text-gray-600 text-lg mb-4">Sie haben noch keine Events erstellt.</p>
                    <a href="{{ route('organizer.events.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                        Erstes Event erstellen
                    </a>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategorie</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Datum</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buchungen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($events as $event)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $event->venue_city }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $event->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $event->start_date->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $event->bookings->count() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($event->is_published)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Veröffentlicht
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Entwurf
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('events.show', $event->slug) }}" class="text-blue-600 hover:text-blue-900 mr-3">Ansehen</a>
                                        <a href="{{ route('organizer.events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Bearbeiten</a>
                                        <form action="{{ route('organizer.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

