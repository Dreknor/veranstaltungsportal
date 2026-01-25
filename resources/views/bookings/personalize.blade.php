<x-layouts.public :title="'Tickets personalisieren - ' . $booking->event->title">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('bookings.show', $booking->booking_number) }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    <x-icon.arrow-left class="w-4 h-4 mr-2" />
                    Zurück zur Buchung
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Tickets personalisieren</h1>
                <p class="text-lg text-gray-600 mt-2">{{ $booking->event->title }}</p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-blue-900 mb-2">Wichtig: Personalisierung erforderlich</h3>
                        <p class="text-blue-800 text-sm">
                            Sie haben mehrere Tickets gebucht. Bitte geben Sie für jedes Ticket die Daten des jeweiligen Teilnehmers an.
                            Die Tickets werden erst nach der Personalisierung per E-Mail versendet.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Personalization Form -->
            <form method="POST" action="{{ route('bookings.save-personalization', $booking->booking_number) }}">
                @csrf

                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Teilnehmerdaten</h2>

                    <div class="space-y-6">
                        @foreach($booking->items as $index => $item)
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <h3 class="font-semibold text-gray-900 mb-4">
                                    Ticket {{ $index + 1 }}: {{ $item->ticketType->name }}
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="attendee_name_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            Name des Teilnehmers *
                                        </label>
                                        <input
                                            type="text"
                                            id="attendee_name_{{ $item->id }}"
                                            name="attendees[{{ $item->id }}][attendee_name]"
                                            value="{{ old('attendees.' . $item->id . '.attendee_name', $item->attendee_name ?? ($index === 0 ? $booking->customer_name : '')) }}"
                                            required
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="Vor- und Nachname"
                                        >
                                        @error('attendees.' . $item->id . '.attendee_name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="attendee_email_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                                            E-Mail des Teilnehmers *
                                        </label>
                                        <input
                                            type="email"
                                            id="attendee_email_{{ $item->id }}"
                                            name="attendees[{{ $item->id }}][attendee_email]"
                                            value="{{ old('attendees.' . $item->id . '.attendee_email', $item->attendee_email ?? ($index === 0 ? $booking->customer_email : '')) }}"
                                            required
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="email@beispiel.de"
                                        >
                                        @error('attendees.' . $item->id . '.attendee_email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex items-center justify-between">
                        <a href="{{ route('bookings.show', $booking->booking_number) }}"
                           class="text-gray-600 hover:text-gray-800">
                            Abbrechen
                        </a>
                        <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            Personalisierung speichern und Tickets versenden
                        </button>
                    </div>
                </div>
            </form>

            <!-- Help Text -->
            <div class="mt-6 text-sm text-gray-600 bg-white rounded-lg p-4 shadow-sm">
                <p class="font-semibold mb-2">Hinweise:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li>Alle Felder sind Pflichtfelder.</li>
                    <li>Die Tickets werden nach der Personalisierung automatisch an die E-Mail-Adresse des Käufers versendet.</li>
                    <li>Jedes Ticket wird auf den Namen des jeweiligen Teilnehmers ausgestellt.</li>
                    <li>Bei Fragen wenden Sie sich bitte an den Veranstalter.</li>
                </ul>
            </div>
        </div>
    </div>
</x-layouts.public>
