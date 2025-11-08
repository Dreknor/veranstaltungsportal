<x-layouts.app title="Event erstellen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('organizer.events.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <x-icon.arrow-left class="w-4 h-4 mr-2" />
                    Zurück zur Übersicht
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Neues Event erstellen</h1>
            </div>

            <form method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Grunddaten -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Grunddaten</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titel *</label>
                            <input type="text" id="title" name="title" required value="{{ old('title') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="event_category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategorie *</label>
                            <select id="event_category_id" name="event_category_id" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Bitte wählen...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('event_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Veranstaltungsart *</label>
                            <select id="event_type" name="event_type" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="physical" {{ old('event_type', 'physical') == 'physical' ? 'selected' : '' }}>Präsenzveranstaltung</option>
                                <option value="online" {{ old('event_type') == 'online' ? 'selected' : '' }}>Online-Veranstaltung</option>
                                <option value="hybrid" {{ old('event_type') == 'hybrid' ? 'selected' : '' }}>Hybrid (Präsenz & Online)</option>
                            </select>
                            @error('event_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Beschreibung *</label>
                            <textarea id="description" name="description" required rows="6"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-1">Titelbild</label>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*"
                                   class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                            @error('featured_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Datum & Zeit -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Datum & Zeit</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start *</label>
                            <input type="datetime-local" id="start_date" name="start_date" required value="{{ old('start_date') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Ende *</label>
                            <input type="datetime-local" id="end_date" name="end_date" required value="{{ old('end_date') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Veranstaltungsort (Nur für Präsenz & Hybrid) -->
                <div id="venue-section" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Veranstaltungsort</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="venue_name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="venue-required">*</span></label>
                            <input type="text" id="venue_name" name="venue_name" value="{{ old('venue_name') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="venue_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse <span class="venue-required">*</span></label>
                            <input type="text" id="venue_address" name="venue_address" value="{{ old('venue_address') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="venue_postal_code" class="block text-sm font-medium text-gray-700 mb-1">PLZ <span class="venue-required">*</span></label>
                                <input type="text" id="venue_postal_code" name="venue_postal_code" value="{{ old('venue_postal_code') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('venue_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="venue_city" class="block text-sm font-medium text-gray-700 mb-1">Stadt <span class="venue-required">*</span></label>
                                <input type="text" id="venue_city" name="venue_city" value="{{ old('venue_city') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('venue_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="venue_country" class="block text-sm font-medium text-gray-700 mb-1">Land <span class="venue-required">*</span></label>
                            <input type="text" id="venue_country" name="venue_country" value="{{ old('venue_country', 'Germany') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="venue_latitude" class="block text-sm font-medium text-gray-700 mb-1">Breitengrad</label>
                                <input type="number" step="0.0000001" id="venue_latitude" name="venue_latitude" value="{{ old('venue_latitude') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="venue_longitude" class="block text-sm font-medium text-gray-700 mb-1">Längengrad</label>
                                <input type="number" step="0.0000001" id="venue_longitude" name="venue_longitude" value="{{ old('venue_longitude') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="directions" class="block text-sm font-medium text-gray-700 mb-1">Anfahrtsbeschreibung</label>
                            <textarea id="directions" name="directions" rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('directions') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Online-Zugang (Nur für Online & Hybrid) -->
                <div id="online-section" class="bg-white rounded-lg shadow-md p-6" style="display: none;">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Online-Zugang</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="online_url" class="block text-sm font-medium text-gray-700 mb-1">Meeting/Konferenz-URL <span class="online-required">*</span></label>
                            <input type="url" id="online_url" name="online_url" value="{{ old('online_url') }}"
                                   placeholder="https://zoom.us/j/123456789 oder https://meet.google.com/..."
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Diese URL wird erst nach der Zahlung an die Teilnehmer weitergegeben.</p>
                            @error('online_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="online_access_code" class="block text-sm font-medium text-gray-700 mb-1">Zugangs-/Meeting-Code (optional)</label>
                            <input type="text" id="online_access_code" name="online_access_code" value="{{ old('online_access_code') }}"
                                   placeholder="z.B. Meeting-ID, Passwort"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Optional: Falls ein separater Zugangscode für die Online-Veranstaltung benötigt wird.</p>
                            @error('online_access_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Zusatzinformationen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Zusatzinformationen</h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price_from" class="block text-sm font-medium text-gray-700 mb-1">Preis ab (€)</label>
                                <input type="number" step="0.01" id="price_from" name="price_from" value="{{ old('price_from') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="max_attendees" class="block text-sm font-medium text-gray-700 mb-1">Max. Teilnehmer</label>
                                <input type="number" id="max_attendees" name="max_attendees" value="{{ old('max_attendees') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                            <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="livestream_url" class="block text-sm font-medium text-gray-700 mb-1">Livestream URL</label>
                            <input type="url" id="livestream_url" name="livestream_url" value="{{ old('livestream_url') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Event veröffentlichen</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Als Featured markieren</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="is_private" value="1" id="is_private" {{ old('is_private') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Privates Event (Zugriffscode erforderlich)</span>
                            </label>
                        </div>

                        <div id="access_code_field" style="display: {{ old('is_private') ? 'block' : 'none' }}">
                            <label for="access_code" class="block text-sm font-medium text-gray-700 mb-1">Zugriffscode</label>
                            <input type="text" id="access_code" name="access_code" value="{{ old('access_code') }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Veranstalter-Informationen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Veranstalter-Informationen</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="organizer_info" class="block text-sm font-medium text-gray-700 mb-1">Informationen</label>
                            <textarea id="organizer_info" name="organizer_info" rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('organizer_info') }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="organizer_email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                                <input type="email" id="organizer_email" name="organizer_email" value="{{ old('organizer_email') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="organizer_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input type="tel" id="organizer_phone" name="organizer_phone" value="{{ old('organizer_phone') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="organizer_website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input type="url" id="organizer_website" name="organizer_website" value="{{ old('organizer_website') }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('organizer.events.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Abbrechen
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Event erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Toggle Access Code field
        document.getElementById('is_private').addEventListener('change', function() {
            document.getElementById('access_code_field').style.display = this.checked ? 'block' : 'none';
        });

        // Toggle Venue/Online sections based on event type
        const eventTypeSelect = document.getElementById('event_type');
        const venueSection = document.getElementById('venue-section');
        const onlineSection = document.getElementById('online-section');

        function updateSections() {
            const eventType = eventTypeSelect.value;

            if (eventType === 'physical') {
                venueSection.style.display = 'block';
                onlineSection.style.display = 'none';
                setVenueFieldsRequired(true);
                setOnlineFieldsRequired(false);
            } else if (eventType === 'online') {
                venueSection.style.display = 'none';
                onlineSection.style.display = 'block';
                setVenueFieldsRequired(false);
                setOnlineFieldsRequired(true);
            } else if (eventType === 'hybrid') {
                venueSection.style.display = 'block';
                onlineSection.style.display = 'block';
                setVenueFieldsRequired(true);
                setOnlineFieldsRequired(true);
            }
        }

        function setVenueFieldsRequired(required) {
            ['venue_name', 'venue_address', 'venue_city', 'venue_postal_code', 'venue_country'].forEach(id => {
                const field = document.getElementById(id);
                if (field) {
                    field.required = required;
                }
            });
            document.querySelectorAll('.venue-required').forEach(el => {
                el.style.display = required ? 'inline' : 'none';
            });
        }

        function setOnlineFieldsRequired(required) {
            const onlineUrl = document.getElementById('online_url');
            if (onlineUrl) {
                onlineUrl.required = required;
            }
            document.querySelectorAll('.online-required').forEach(el => {
                el.style.display = required ? 'inline' : 'none';
            });
        }

        eventTypeSelect.addEventListener('change', updateSections);

        // Initialize on page load
        updateSections();
    </script>
    @endpush
</x-layouts.app>

