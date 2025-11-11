<x-layouts.app title="Individuelle Gebühren für {{ $user->name }}">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Individuelle Gebühren</h1>
                        <p class="text-gray-600 mt-2">Ausnahme-Einstellungen für: <strong>{{ $user->name }}</strong></p>
                    </div>
                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-800">
                        ← Zurück zum Profil
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Global Settings Info -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-sm font-medium text-blue-800 mb-3">Globale Plattform-Einstellungen</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm text-blue-700">
                    <div>
                        <span class="font-medium">Typ:</span>
                        {{ $globalSettings['fee_type'] === 'percentage' ? 'Prozentual' : 'Festbetrag' }}
                    </div>
                    @if($globalSettings['fee_type'] === 'percentage')
                        <div>
                            <span class="font-medium">Gebühr:</span>
                            {{ $globalSettings['fee_percentage'] }}%
                        </div>
                    @else
                        <div>
                            <span class="font-medium">Gebühr:</span>
                            {{ number_format($globalSettings['fee_fixed_amount'], 2, ',', '.') }} €
                        </div>
                    @endif
                    <div>
                        <span class="font-medium">Mindestgebühr:</span>
                        {{ number_format($globalSettings['minimum_fee'], 2, ',', '.') }} €
                    </div>
                    <div>
                        <span class="font-medium">Gilt für:</span>
                        Alle Organisatoren
                    </div>
                </div>
            </div>

            <!-- Custom Fee Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('admin.organizer-fees.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Enable Custom Fee -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="use_custom_fee" id="use_custom_fee" value="1"
                                       {{ old('use_custom_fee', $customFee['enabled'] ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3">
                                <label for="use_custom_fee" class="font-medium text-gray-700">
                                    Individuelle Gebühren aktivieren
                                </label>
                                <p class="text-sm text-gray-500">
                                    Überschreibt die globalen Einstellungen für diesen Organisator
                                </p>
                            </div>
                        </div>

                        <div id="custom_fee_settings" style="display: none;">
                            <!-- Fee Type -->
                            <div class="border-t border-gray-200 pt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Gebührentyp</label>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="radio" name="custom_fee_type" id="custom_type_percentage" value="percentage"
                                               {{ old('custom_fee_type', $customFee['fee_type'] ?? 'percentage') == 'percentage' ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="custom_type_percentage" class="ml-3 block text-sm text-gray-700">
                                            Prozentual
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="custom_fee_type" id="custom_type_fixed" value="fixed"
                                               {{ old('custom_fee_type', $customFee['fee_type'] ?? 'percentage') == 'fixed' ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <label for="custom_type_fixed" class="ml-3 block text-sm text-gray-700">
                                            Festbetrag
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Percentage Field -->
                            <div id="custom_percentage_field">
                                <label for="custom_fee_percentage" class="block text-sm font-medium text-gray-700">
                                    Plattformgebühr (Prozent)
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="custom_fee_percentage" id="custom_fee_percentage"
                                           step="0.01" min="0" max="100"
                                           value="{{ old('custom_fee_percentage', $customFee['fee_percentage'] ?? 0) }}"
                                           class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">%</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Beispiel: Bei 3% und 100€ Buchung werden 3€ Gebühr berechnet
                                </p>
                                @error('custom_fee_percentage')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Fixed Amount Field -->
                            <div id="custom_fixed_field" style="display: none;">
                                <label for="custom_fee_fixed_amount" class="block text-sm font-medium text-gray-700">
                                    Plattformgebühr (Festbetrag)
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="custom_fee_fixed_amount" id="custom_fee_fixed_amount"
                                           step="0.01" min="0"
                                           value="{{ old('custom_fee_fixed_amount', $customFee['fee_fixed_amount'] ?? 0) }}"
                                           class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">€</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Fester Betrag pro Buchung
                                </p>
                                @error('custom_fee_fixed_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Minimum Fee Field -->
                            <div>
                                <label for="custom_minimum_fee" class="block text-sm font-medium text-gray-700">
                                    Mindestgebühr pro Buchung
                                </label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" name="custom_minimum_fee" id="custom_minimum_fee"
                                           step="0.01" min="0"
                                           value="{{ old('custom_minimum_fee', $customFee['minimum_fee'] ?? $globalSettings['minimum_fee']) }}"
                                           class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">€</span>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">
                                    Mindestgebühr pro Buchung - wichtig für kostenlose Tickets. Standard: {{ number_format($globalSettings['minimum_fee'], 2, ',', '.') }} €
                                </p>
                                @error('custom_minimum_fee')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="custom_fee_notes" class="block text-sm font-medium text-gray-700">
                                    Notizen (optional)
                                </label>
                                <textarea name="custom_fee_notes" id="custom_fee_notes" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Grund für die individuelle Regelung..."
                                >{{ old('custom_fee_notes', $customFee['notes'] ?? '') }}</textarea>
                                <p class="mt-2 text-sm text-gray-500">
                                    Interne Notiz, warum dieser Organisator spezielle Konditionen erhält
                                </p>
                                @error('custom_fee_notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @if(!empty($customFee['updated_at']))
                            <div class="text-sm text-gray-500 border-t border-gray-200 pt-4">
                                Letzte Aktualisierung: {{ \Carbon\Carbon::parse($customFee['updated_at'])->format('d.m.Y H:i') }} Uhr
                            </div>
                        @endif

                        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                            @if(!empty($customFee))
                                <form method="POST" action="{{ route('admin.organizer-fees.destroy', $user) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium"
                                            onclick="return confirm('Individuelle Gebühren wirklich entfernen?')">
                                        Individuelle Gebühren entfernen
                                    </button>
                                </form>
                            @else
                                <div></div>
                            @endif

                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Einstellungen speichern
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Wichtiger Hinweis</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Individuelle Gebühren gelten für alle neuen Buchungen dieses Organisators</li>
                                <li>Bestehende Rechnungen werden nicht rückwirkend angepasst</li>
                                <li>Bei Deaktivierung gelten wieder die globalen Einstellungen</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const useCustomFeeCheckbox = document.getElementById('use_custom_fee');
        const customFeeSettings = document.getElementById('custom_fee_settings');
        const percentageRadio = document.getElementById('custom_type_percentage');
        const fixedRadio = document.getElementById('custom_type_fixed');
        const percentageField = document.getElementById('custom_percentage_field');
        const fixedField = document.getElementById('custom_fixed_field');

        function toggleCustomSettings() {
            if (useCustomFeeCheckbox.checked) {
                customFeeSettings.style.display = 'block';
                toggleFeeFields();
            } else {
                customFeeSettings.style.display = 'none';
            }
        }

        function toggleFeeFields() {
            if (percentageRadio.checked) {
                percentageField.style.display = 'block';
                fixedField.style.display = 'none';
            } else {
                percentageField.style.display = 'none';
                fixedField.style.display = 'block';
            }
        }

        useCustomFeeCheckbox.addEventListener('change', toggleCustomSettings);
        percentageRadio.addEventListener('change', toggleFeeFields);
        fixedRadio.addEventListener('change', toggleFeeFields);

        // Initialize on page load
        toggleCustomSettings();
    </script>
    @endpush
</x-layouts.app>

