<x-layouts.app title="Event bearbeiten">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('organizer.events.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    ← Zurück zur Übersicht
                </a>
                <div class="flex items-center justify-between mt-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Event bearbeiten</h1>
                        @if($event->is_cancelled)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500 text-white mt-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                ABGESAGT
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if($event->is_cancelled)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-red-900">Dieses Event wurde abgesagt</p>
                            <p class="text-sm text-red-700 mt-1">Abgesagt am: {{ $event->cancelled_at->format('d.m.Y H:i') }} Uhr</p>
                            @if($event->cancellation_reason)
                                <p class="text-sm text-red-700 mt-1"><strong>Grund:</strong> {{ $event->cancellation_reason }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Teilnehmer-Management -->
            @if($event->hasAttendees())
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Teilnehmer-Management</h2>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                            </svg>
                            <span class="text-gray-700 font-medium">{{ $event->getAttendeesCount() }} bestätigte Teilnehmer</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('organizer.events.attendees.download', $event) }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Teilnehmerliste herunterladen
                        </a>

                        <a href="{{ route('organizer.events.attendees.contact', $event) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Teilnehmer kontaktieren
                        </a>

                        <a href="{{ route('organizer.events.waitlist.index', $event) }}"
                           class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Warteliste verwalten
                            @php
                                $waitlistCount = \App\Models\EventWaitlist::where('event_id', $event->id)
                                    ->where('status', 'waiting')
                                    ->count();
                            @endphp
                            @if($waitlistCount > 0)
                                <span class="ml-2 px-2 py-0.5 bg-yellow-800 text-white rounded-full text-xs font-semibold">
                                    {{ $waitlistCount }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}

                    @if(session('pending_featured_fee_id'))
                        <div class="mt-2">
                            <a href="{{ route('organizer.featured-events.payment', session('pending_featured_fee_id')) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                → Zur Zahlung der Featured Event Gebühr
                            </a>
                        </div>
                    @endif
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
                            <label for="event_type" class="block text-sm font-medium text-gray-700 mb-1">Veranstaltungsart *</label>
                            <select id="event_type" name="event_type" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="physical" {{ old('event_type', $event->event_type ?? 'physical') == 'physical' ? 'selected' : '' }}>Präsenzveranstaltung</option>
                                <option value="online" {{ old('event_type', $event->event_type) == 'online' ? 'selected' : '' }}>Online-Veranstaltung</option>
                                <option value="hybrid" {{ old('event_type', $event->event_type) == 'hybrid' ? 'selected' : '' }}>Hybrid (Präsenz & Online)</option>
                            </select>
                            @error('event_type')
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

                        <div>
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-1">Titelbild</label>
                            @if($event->featured_image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($event->featured_image) }}" alt="Aktuelles Titelbild" class="h-32 w-auto rounded-lg border">
                                    <p class="text-xs text-gray-500 mt-1">Aktuelles Titelbild (wird ersetzt, wenn Sie ein neues hochladen)</p>
                                </div>
                            @endif
                            <input type="file" id="featured_image" name="featured_image" accept="image/*"
                                   class="w-full border-gray-300 rounded-lg focus:border-blue-500 focus:ring-blue-500">
                            @error('featured_image')
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

                <!-- Zusatzinformationen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Zusatzinformationen</h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="price_from" class="block text-sm font-medium text-gray-700 mb-1">Preis ab (€)</label>
                                <input type="number" step="0.01" id="price_from" name="price_from"
                                       value="{{ old('price_from', $event->price_from) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="max_attendees" class="block text-sm font-medium text-gray-700 mb-1">Max. Teilnehmer</label>
                                <input type="number" id="max_attendees" name="max_attendees"
                                       value="{{ old('max_attendees', $event->max_attendees) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Veranstaltungsort (Nur für Präsenz & Hybrid) -->
                <div id="venue-section" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Veranstaltungsort</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="venue_name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="venue-required">*</span></label>
                            <input type="text" id="venue_name" name="venue_name"
                                   value="{{ old('venue_name', $event->venue_name) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="venue_address" class="block text-sm font-medium text-gray-700 mb-1">Adresse <span class="venue-required">*</span></label>
                            <input type="text" id="venue_address" name="venue_address"
                                   value="{{ old('venue_address', $event->venue_address) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2">
                                <label for="venue_city" class="block text-sm font-medium text-gray-700 mb-1">Stadt <span class="venue-required">*</span></label>
                                <input type="text" id="venue_city" name="venue_city"
                                       value="{{ old('venue_city', $event->venue_city) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('venue_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="venue_postal_code" class="block text-sm font-medium text-gray-700 mb-1">PLZ <span class="venue-required">*</span></label>
                                <input type="text" id="venue_postal_code" name="venue_postal_code"
                                       value="{{ old('venue_postal_code', $event->venue_postal_code) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('venue_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="venue_country" class="block text-sm font-medium text-gray-700 mb-1">Land <span class="venue-required">*</span></label>
                            <input type="text" id="venue_country" name="venue_country"
                                   value="{{ old('venue_country', $event->venue_country) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('venue_country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="venue_latitude" class="block text-sm font-medium text-gray-700 mb-1">Breitengrad</label>
                                <input type="number" step="0.0000001" id="venue_latitude" name="venue_latitude"
                                       value="{{ old('venue_latitude', $event->venue_latitude) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="venue_longitude" class="block text-sm font-medium text-gray-700 mb-1">Längengrad</label>
                                <input type="number" step="0.0000001" id="venue_longitude" name="venue_longitude"
                                       value="{{ old('venue_longitude', $event->venue_longitude) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label for="directions" class="block text-sm font-medium text-gray-700 mb-1">Anfahrtsbeschreibung</label>
                            <textarea id="directions" name="directions" rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('directions', $event->directions) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Online-Zugang (Nur für Online & Hybrid) -->
                <div id="online-section" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Online-Zugang</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="online_url" class="block text-sm font-medium text-gray-700 mb-1">Meeting/Konferenz-URL <span class="online-required">*</span></label>
                            <input type="url" id="online_url" name="online_url"
                                   value="{{ old('online_url', $event->online_url) }}"
                                   placeholder="https://zoom.us/j/123456789 oder https://meet.google.com/..."
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Diese URL wird erst nach der Zahlung an die Teilnehmer weitergegeben.</p>
                            @error('online_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="online_access_code" class="block text-sm font-medium text-gray-700 mb-1">Zugangs-/Meeting-Code (optional)</label>
                            <input type="text" id="online_access_code" name="online_access_code"
                                   value="{{ old('online_access_code', $event->online_access_code) }}"
                                   placeholder="z.B. Meeting-ID, Passwort"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Optional: Falls ein separater Zugangscode für die Online-Veranstaltung benötigt wird.</p>
                            @error('online_access_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                            <input type="url" id="video_url" name="video_url"
                                   value="{{ old('video_url', $event->video_url) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="livestream_url" class="block text-sm font-medium text-gray-700 mb-1">Livestream URL</label>
                            <input type="url" id="livestream_url" name="livestream_url"
                                   value="{{ old('livestream_url', $event->livestream_url) }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Einstellungen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Einstellungen</h2>

                    <!-- Cost Overview (shown when about to publish) -->
                    @if(isset($publishingCosts) && ($publishingCosts['total'] > 0 || !$event->is_published))
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded" id="cost-overview">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-blue-800">Geschätzte Kosten bei Veröffentlichung</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    @if(count($publishingCosts['breakdown']) > 0)
                                        <div class="space-y-2">
                                            @foreach($publishingCosts['breakdown'] as $item)
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <div class="font-medium">{{ $item['label'] }}</div>
                                                        @if(isset($item['description']))
                                                            <div class="text-xs text-blue-600">{{ $item['description'] }}</div>
                                                        @endif
                                                        @if(isset($item['status']) && $item['status'] === 'pending')
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                                                Zahlung ausstehend
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="font-semibold ml-4 whitespace-nowrap">
                                                        {{ number_format($item['amount'], 2, ',', '.') }} €
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="border-t border-blue-200 pt-2 mt-2">
                                                <div class="flex justify-between items-center">
                                                    <div class="font-bold text-blue-900">Geschätzte Gesamtkosten (netto):</div>
                                                    <div class="font-bold text-blue-900 text-lg">
                                                        {{ number_format($publishingCosts['total'], 2, ',', '.') }} €
                                                    </div>
                                                </div>
                                                <div class="text-xs text-blue-600 mt-1">
                                                    + {{ number_format($publishingCosts['total'] * 0.19, 2, ',', '.') }} € MwSt. (19%) =
                                                    {{ number_format($publishingCosts['total'] * 1.19, 2, ',', '.') }} € brutto
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <p>Die Kosten werden nach Event-Ende basierend auf den tatsächlichen Buchungen abgerechnet.</p>
                                    @endif

                                    <div class="mt-3 text-xs">
                                        <strong>Hinweis:</strong> Die Plattformgebühren sind Schätzungen basierend auf Ihren Ticket-Einstellungen.
                                        Die tatsächliche Abrechnung erfolgt nach Event-Ende basierend auf den realen Buchungen.
                                        @if($publishingCosts['featured_fees']['active'] && $publishingCosts['featured_fees']['total'] > 0)
                                            Featured Event Gebühren müssen vor der Veröffentlichung bezahlt werden.
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

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

                        <input type="hidden" id="featured_duration_type" name="featured_duration_type" value="">
                        <input type="hidden" id="featured_custom_days" name="featured_custom_days" value="">
                        <input type="hidden" id="featured_start_date" name="featured_start_date" value="">

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

                <!-- Veranstalter-Informationen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Veranstalter-Informationen</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="organizer_info" class="block text-sm font-medium text-gray-700 mb-1">Informationen</label>
                            <textarea id="organizer_info" name="organizer_info" rows="3"
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('organizer_info', $event->organizer_info) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="organizer_email" class="block text-sm font-medium text-gray-700 mb-1">E-Mail</label>
                                <input type="email" id="organizer_email" name="organizer_email"
                                       value="{{ old('organizer_email', $event->organizer_email) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="organizer_phone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
                                <input type="tel" id="organizer_phone" name="organizer_phone"
                                       value="{{ old('organizer_phone', $event->organizer_phone) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="organizer_website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                                <input type="url" id="organizer_website" name="organizer_website"
                                       value="{{ old('organizer_website', $event->organizer_website) }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
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
        // Toggle Access Code field
        document.getElementById('is_private').addEventListener('change', function() {
            document.getElementById('access-code-field').classList.toggle('hidden', !this.checked);
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

        // Dynamic cost calculation when featured checkbox changes
        const featuredCheckbox = document.getElementById('is_featured');
        const costOverview = document.getElementById('cost-overview');

        if (featuredCheckbox && costOverview) {
            const wasInitiallyFeatured = featuredCheckbox.checked;

            featuredCheckbox.addEventListener('change', function() {
                if (this.checked && !wasInitiallyFeatured) {
                    // Opening modal to book featured
                    window.dispatchEvent(new CustomEvent('featured-modal-open'));
                } else if (this.checked) {
                    // Already featured, just update costs
                    updateCostEstimate();
                } else {
                    // Unchecked, clear booking data and update costs
                    document.getElementById('featured_duration_type').value = '';
                    document.getElementById('featured_custom_days').value = '';
                    document.getElementById('featured_start_date').value = '';
                    updateCostEstimate();
                }
            });
        }

        // Listen for featured booking confirmation
        window.addEventListener('featured-booking-confirm', function(e) {
            const { durationType, customDays, startDate } = e.detail;

            // Store in hidden fields
            document.getElementById('featured_duration_type').value = durationType;
            document.getElementById('featured_custom_days').value = customDays;
            document.getElementById('featured_start_date').value = startDate;

            // Keep checkbox checked
            featuredCheckbox.checked = true;

            // Update cost estimate
            updateCostEstimate();
        });

        async function updateCostEstimate() {
            const isFeatured = featuredCheckbox.checked;
            const maxAttendees = document.getElementById('max_attendees')?.value || '';
            const priceFrom = document.getElementById('price_from')?.value || '';

            try {
                const response = await fetch('{{ route('organizer.events.calculate-costs', $event) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        is_featured: isFeatured,
                        max_attendees: maxAttendees,
                        price_from: priceFrom,
                    })
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (data.success && data.costs) {
                    updateCostOverviewUI(data.costs);
                }
            } catch (error) {
                console.error('Error updating cost estimate:', error);
            }
        }

        function updateCostOverviewUI(costs) {
            if (!costOverview) return;

            // Build the HTML for the breakdown
            let breakdownHTML = '';

            if (costs.breakdown && costs.breakdown.length > 0) {
                breakdownHTML = '<div class="space-y-2">';

                costs.breakdown.forEach(item => {
                    let statusBadge = '';
                    if (item.status === 'pending') {
                        statusBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">Zahlung ausstehend</span>';
                    }

                    breakdownHTML += `
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium">${item.label}</div>
                                ${item.description ? `<div class="text-xs text-blue-600">${item.description}</div>` : ''}
                                ${statusBadge}
                            </div>
                            <div class="font-semibold ml-4 whitespace-nowrap">
                                ${formatCurrency(item.amount)}
                            </div>
                        </div>
                    `;
                });

                const vatAmount = costs.total * 0.19;
                const totalWithVat = costs.total * 1.19;

                breakdownHTML += `
                    <div class="border-t border-blue-200 pt-2 mt-2">
                        <div class="flex justify-between items-center">
                            <div class="font-bold text-blue-900">Geschätzte Gesamtkosten (netto):</div>
                            <div class="font-bold text-blue-900 text-lg">
                                ${formatCurrency(costs.total)}
                            </div>
                        </div>
                        <div class="text-xs text-blue-600 mt-1">
                            + ${formatCurrency(vatAmount)} MwSt. (19%) = ${formatCurrency(totalWithVat)} brutto
                        </div>
                    </div>
                `;
                breakdownHTML += '</div>';
            } else {
                breakdownHTML = '<p>Die Kosten werden nach Event-Ende basierend auf den tatsächlichen Buchungen abgerechnet.</p>';
            }

            // Find the content area and update it
            const contentArea = costOverview.querySelector('.text-sm.text-blue-700');
            if (contentArea) {
                contentArea.innerHTML = breakdownHTML + `
                    <div class="mt-3 text-xs">
                        <strong>Hinweis:</strong> Die Plattformgebühren sind Schätzungen basierend auf Ihren Ticket-Einstellungen.
                        Die tatsächliche Abrechnung erfolgt nach Event-Ende basierend auf den realen Buchungen.
                        ${costs.featured_fees && costs.featured_fees.active && costs.featured_fees.total > 0 ?
                            'Featured Event Gebühren müssen vor der Veröffentlichung bezahlt werden.' : ''}
                    </div>
                `;
            }

            // Show/hide the cost overview based on whether there are costs
            if (costs.total > 0 || !{{ $event->is_published ? 'true' : 'false' }}) {
                costOverview.style.display = 'block';
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('de-DE', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }
    </script>

    <!-- Featured Booking Modal -->
    <x-featured-booking-modal :event="$event" />
</x-layouts.app>

