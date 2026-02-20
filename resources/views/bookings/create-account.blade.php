<x-layouts.public :title="'Konto erstellen'">
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    @if($existingUser)
                        Buchung mit Konto verknüpfen
                    @else
                        Benutzerkonto erstellen
                    @endif
                </h1>
                <p class="text-gray-600">
                    Buchung: <span class="font-mono font-semibold">{{ $booking->booking_number }}</span>
                </p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md p-8">
                @if($existingUser)
                    <!-- Link to existing account -->
                    <div class="mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-blue-900 mb-1">Bestehendes Konto gefunden</h3>
                                    <p class="text-sm text-blue-800">
                                        Für die E-Mail-Adresse <strong>{{ $booking->customer_email }}</strong> existiert bereits ein Konto.
                                        Bitte geben Sie Ihr Passwort ein, um diese Buchung mit Ihrem Konto zu verknüpfen.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('bookings.link-account', $booking->booking_number) }}">
                            @csrf

                            <div class="mb-6">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Passwort
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Ihr Passwort">
                                @error('password')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-4">
                                <button type="submit"
                                        class="flex-1 bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                                    Buchung verknüpfen
                                </button>
                                <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                   class="flex-1 bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg hover:bg-gray-300 transition text-center">
                                    Abbrechen
                                </a>
                            </div>
                        </form>

                        <div class="mt-6 pt-6 border-t">
                            <p class="text-sm text-gray-600 text-center mb-4">
                                Passwort vergessen?
                            </p>
                            <a href="{{ route('password.request') }}"
                               class="text-sm text-blue-600 hover:text-blue-800 block text-center">
                                Passwort zurücksetzen
                            </a>
                        </div>
                    </div>

                    <!-- Option to create new account instead -->
                    <div class="mt-8 pt-6 border-t">
                        <p class="text-sm text-gray-600 text-center mb-4">
                            Möchten Sie stattdessen ein neues Konto erstellen?
                        </p>
                        <p class="text-xs text-gray-500 text-center">
                            Dies wird ein separates Konto mit einer anderen E-Mail-Adresse sein.
                        </p>
                    </div>

                @else
                    <!-- Create new account -->
                    <div class="mb-6">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex items-start">
                                <svg class="w-6 h-6 text-green-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
                                </svg>
                                <div>
                                    <h3 class="font-semibold text-green-900 mb-1">Vorteile eines Benutzerkontos</h3>
                                    <ul class="text-sm text-green-800 space-y-1">
                                        <li>✓ Alle Buchungen zentral verwalten</li>
                                        <li>✓ Schnellerer Checkout bei zukünftigen Buchungen</li>
                                        <li>✓ Persönliche Empfehlungen erhalten</li>
                                        <li>✓ Teilnahmezertifikate automatisch speichern</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('bookings.store-account', $booking->booking_number) }}">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    E-Mail-Adresse
                                </label>
                                <input type="email"
                                       value="{{ $booking->customer_email }}"
                                       disabled
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                                <p class="text-xs text-gray-500 mt-1">Diese E-Mail-Adresse wird für Ihr Konto verwendet.</p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Name
                                </label>
                                <input type="text"
                                       value="{{ $booking->customer_name }}"
                                       disabled
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100">
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Passwort <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       id="password"
                                       name="password"
                                       required
                                       minlength="8"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Mindestens 8 Zeichen">
                                @error('password')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Passwort bestätigen <span class="text-red-500">*</span>
                                </label>
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required
                                       minlength="8"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Passwort wiederholen">
                                @error('password_confirmation')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="flex items-start">
                                    <input type="checkbox"
                                           name="terms"
                                           required
                                           class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">
                                        Ich akzeptiere die <a href="#" class="text-blue-600 hover:text-blue-800">Nutzungsbedingungen</a>
                                        und <a href="#" class="text-blue-600 hover:text-blue-800">Datenschutzerklärung</a>
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-4">
                                <button type="submit"
                                        class="flex-1 bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                                    Konto erstellen
                                </button>
                                <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                   class="flex-1 bg-gray-200 text-gray-700 font-semibold px-6 py-3 rounded-lg hover:bg-gray-300 transition text-center">
                                    Später
                                </a>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Back to booking -->
            <div class="text-center mt-6">
                <a href="{{ route('bookings.show', $booking->booking_number) }}"
                   class="text-sm text-gray-600 hover:text-gray-800">
                    ← Zurück zur Buchungsübersicht
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
