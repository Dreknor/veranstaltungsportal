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

            @if(!auth()->user()->canPublishEvents())
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-yellow-900">Veröffentlichung nicht möglich</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                Um Events zu veröffentlichen, müssen Sie zunächst Ihre
                                @if(!auth()->user()->hasCompleteBillingData() && !auth()->user()->hasCompleteBankAccount())
                                    Rechnungsdaten und Bankverbindung
                                @elseif(!auth()->user()->hasCompleteBillingData())
                                    Rechnungsdaten
                                @else
                                    Bankverbindung
                                @endif
                                vervollständigen.
                            </p>
                            <p class="text-sm text-yellow-600 mt-2">
                                Diese Angaben sind notwendig, da bei Buchungen automatisch Rechnungen an Teilnehmer versendet werden.
                            </p>
                            <div class="flex gap-2 mt-3">
                                @if(!auth()->user()->hasCompleteBillingData())
                                    <a href="{{ route('organizer.bank-account.billing-data') }}"
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Rechnungsdaten vervollständigen
                                    </a>
                                @endif
                                @if(!auth()->user()->hasCompleteBankAccount())
                                    <a href="{{ route('organizer.bank-account.index') }}"
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm font-medium">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Bankverbindung vervollständigen
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('organizer.events.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Einstellungen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Einstellungen</h2>

                    <!-- Cost Overview -->
                    @if(isset($publishingCosts))
                    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded" id="cost-overview">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-medium text-blue-800">Geschätzte Kosten bei Veröffentlichung</h3>
                                <div class="mt-2 text-sm text-blue-700" id="cost-breakdown-content">
                                    <p>Die Kosten werden nach Event-Ende basierend auf den tatsächlichen Buchungen abgerechnet.</p>
                                    <div class="mt-3 text-xs">
                                        <strong>Hinweis:</strong> Wenn Sie "Als Featured markieren" wählen, können Sie direkt den Zeitraum buchen.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="space-y-3">
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

                    <div class="mt-4">
                        <!-- Hidden field ensures false value is sent when checkbox is unchecked -->
                        <input type="hidden" name="has_multiple_dates" value="0">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" id="has_multiple_dates" name="has_multiple_dates" value="1" {{ old('has_multiple_dates') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 mt-1">
                            <span class="ml-2">
                                <span class="block text-sm font-medium text-gray-700">Event hat mehrere Termine</span>
                                <span class="block text-xs text-gray-500 mt-1">
                                    Aktivieren Sie diese Option, wenn die Veranstaltung aus mehreren aufeinanderfolgenden Terminen besteht (z.B. ein 8-Wochen-Kurs).
                                    Teilnehmer buchen dann automatisch alle Termine gemeinsam.
                                </span>
                            </span>
                        </label>

                        <!-- Info Box für Multiple Dates -->
                        <div id="multiple-dates-info" class="mt-3 p-4 bg-blue-50 border-l-4 border-blue-500 rounded" style="display: {{ old('has_multiple_dates') ? 'block' : 'none' }}">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-blue-900">Mehrere Termine aktiviert ✓</p>
                                    <p class="text-xs text-blue-700 mt-1">
                                        Das oben angegebene Start- und Enddatum wird als <strong>erster Termin</strong> gespeichert.
                                        Nach dem Erstellen des Events können Sie auf der Bearbeitungsseite weitere Termine hinzufügen.
                                    </p>
                                    <ul class="mt-2 text-xs text-blue-700 space-y-1 list-disc list-inside">
                                        <li>Alle Termine werden gemeinsam gebucht</li>
                                        <li>Die Kapazität gilt für die gesamte Serie</li>
                                        <li>Sie können jeden Termin einzeln absagen</li>
                                    </ul>
                                    <div class="mt-3 flex items-center gap-2 text-xs bg-white rounded p-2 border border-blue-200">
                                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        <span class="text-blue-800">
                                            <strong>So geht's weiter:</strong> Klicken Sie unten auf "Event erstellen". Auf der nächsten Seite können Sie dann weitere Termine hinzufügen.
                                        </span>
                                    </div>
                                </div>
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


                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_published" value="1"
                                       {{ old('is_published') ? 'checked' : '' }}
                                       {{ !auth()->user()->canPublishEvents() ? 'disabled' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 {{ !auth()->user()->canPublishEvents() ? 'opacity-50 cursor-not-allowed' : '' }}">
                                <span class="ml-2 text-sm font-medium text-gray-700 {{ !auth()->user()->canPublishEvents() ? 'opacity-50' : '' }}">
                                    Event veröffentlichen
                                    @if(!auth()->user()->canPublishEvents())
                                        <span class="text-yellow-600 text-xs">(Rechnungsdaten erforderlich)</span>
                                    @endif
                                </span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Als Featured markieren</span>
                            </label>

                            <input type="hidden" id="featured_duration_type" name="featured_duration_type" value="">
                            <input type="hidden" id="featured_custom_days" name="featured_custom_days" value="">
                            <input type="hidden" id="featured_start_date" name="featured_start_date" value="">

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

                <!-- Ticket-Einstellungen -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Ticket-Einstellungen</h2>

                    <div class="space-y-4">
                        <div>
                            <label for="ticket_notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Hinweise auf dem Ticket
                                <span class="text-gray-500 text-xs font-normal">(optional)</span>
                            </label>
                            <textarea id="ticket_notes" name="ticket_notes" rows="4"
                                      placeholder="z.B. Bitte Personalausweis mitbringen, Einlass ab 18:30 Uhr, etc."
                                      class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('ticket_notes') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Diese Hinweise werden auf allen generierten Tickets für diese Veranstaltung angezeigt.
                                Maximal 1000 Zeichen.
                            </p>
                            @error('ticket_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="hidden" name="show_qr_code_on_ticket" value="0">
                                <input type="checkbox" name="show_qr_code_on_ticket" value="1"
                                       {{ old('show_qr_code_on_ticket', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">QR-Code auf Tickets anzeigen</span>
                            </label>
                            <p class="mt-1 ml-6 text-xs text-gray-500">
                                Wenn aktiviert, wird auf jedem Ticket ein QR-Code für den Check-In angezeigt.
                            </p>
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

        // Toggle Multiple Dates Info Box
        const multipleDatesCheckbox = document.getElementById('has_multiple_dates');
        const multipleDatesInfo = document.getElementById('multiple-dates-info');

        if (multipleDatesCheckbox && multipleDatesInfo) {
            multipleDatesCheckbox.addEventListener('change', function() {
                multipleDatesInfo.style.display = this.checked ? 'block' : 'none';

                // Visuelle Rückmeldung
                if (this.checked) {
                    multipleDatesInfo.classList.add('animate-fadeIn');
                }
            });
        }

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

        // Featured Event Handling
        const featuredCheckbox = document.getElementById('is_featured');

        if (featuredCheckbox) {
            featuredCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    // Open modal for booking
                    window.dispatchEvent(new CustomEvent('featured-modal-open'));
                } else {
                    // Clear hidden fields
                    document.getElementById('featured_duration_type').value = '';
                    document.getElementById('featured_custom_days').value = '';
                    document.getElementById('featured_start_date').value = '';
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

            // Update cost overview
            updateCostEstimateForFeatured(durationType, customDays);
        });

        function updateCostEstimateForFeatured(durationType, customDays) {
            const rates = {
                'daily': {{ config('monetization.featured_event_rates.daily', 5.00) }},
                'weekly': {{ config('monetization.featured_event_rates.weekly', 25.00) }},
                'monthly': {{ config('monetization.featured_event_rates.monthly', 80.00) }},
                'custom': customDays * {{ config('monetization.featured_event_rates.daily', 5.00) }}
            };

            const amount = rates[durationType];
            const vatAmount = amount * 0.19;
            const totalAmount = amount * 1.19;

            const durationLabels = {
                'daily': '1 Tag',
                'weekly': '7 Tage',
                'monthly': '30 Tage',
                'custom': customDays + ' Tage'
            };

            const content = document.getElementById('cost-breakdown-content');
            if (content) {
                content.innerHTML = `
                    <div class="space-y-2">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-medium">Featured Event Gebühr (geschätzt)</div>
                                <div class="text-xs text-blue-600">Standardpreis für ${durationLabels[durationType]} Featured-Zeitraum</div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                    Wird bei Erstellung gebucht
                                </span>
                            </div>
                            <div class="font-semibold ml-4 whitespace-nowrap">
                                ${formatCurrency(amount)}
                            </div>
                        </div>

                        <div class="border-t border-blue-200 pt-2 mt-2">
                            <div class="flex justify-between items-center">
                                <div class="font-bold text-blue-900">Geschätzte Gesamtkosten (netto):</div>
                                <div class="font-bold text-blue-900 text-lg">
                                    ${formatCurrency(amount)}
                                </div>
                            </div>
                            <div class="text-xs text-blue-600 mt-1">
                                + ${formatCurrency(vatAmount)} MwSt. (19%) = ${formatCurrency(totalAmount)} brutto
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 text-xs">
                        <strong>Hinweis:</strong> Die Featured Event Gebühr wird nach der Event-Erstellung zur Zahlung fällig.
                        Plattformgebühren für Buchungen werden nach Event-Ende abgerechnet.
                    </div>
                `;
            }
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('de-DE', {
                style: 'currency',
                currency: 'EUR'
            }).format(amount);
        }
    </script>
    @endpush

    <!-- Featured Booking Modal -->
    <x-featured-booking-modal :event="new \App\Models\Event()" />
</x-layouts.app>

