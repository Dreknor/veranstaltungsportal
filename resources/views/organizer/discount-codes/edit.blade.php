<x-layouts.app title="Rabattcode bearbeiten: {{ $discountCode->code }}">
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-8">
        <a href="{{ route('organizer.events.discount-codes.index', $event) }}"
           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center gap-2 mb-4">
            ← Zurück zur Übersicht
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rabattcode bearbeiten</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $discountCode->code }}</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <form action="{{ route('organizer.events.discount-codes.update', [$event, $discountCode]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $discountCode->code) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 uppercase">
                            <p class="mt-1 text-xs text-gray-500">Der Code wird automatisch in Großbuchstaben umgewandelt</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Rabatt-Typ *</label>
                                <select name="type" id="type" required onchange="updateValueLabel()"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="percentage" {{ old('type', $discountCode->type) == 'percentage' ? 'selected' : '' }}>Prozent (%)</option>
                                    <option value="fixed" {{ old('type', $discountCode->type) == 'fixed' ? 'selected' : '' }}>Festbetrag (€)</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="value" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span id="value-label">Wert ({{ $discountCode->type == 'percentage' ? '%' : '€' }}) *</span>
                                </label>
                                <input type="number" name="value" id="value" step="0.01" min="0" value="{{ old('value', $discountCode->value) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-2">Maximale Verwendungen</label>
                            <input type="number" name="max_uses" id="max_uses" min="1" value="{{ old('max_uses', $discountCode->max_uses) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Leer lassen für unbegrenzt (Aktuell verwendet: {{ $discountCode->uses }})</p>
                            @error('max_uses')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">Gültig ab</label>
                                <input type="datetime-local" name="valid_from" id="valid_from"
                                       value="{{ old('valid_from', $discountCode->valid_from ? $discountCode->valid_from->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('valid_from')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">Gültig bis</label>
                                <input type="datetime-local" name="valid_until" id="valid_until"
                                       value="{{ old('valid_until', $discountCode->valid_until ? $discountCode->valid_until->format('Y-m-d\TH:i') : '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('valid_until')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $discountCode->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Aktiv</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end space-x-4">
                            <a href="{{ route('organizer.events.discount-codes.index', $event) }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Abbrechen
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Änderungen speichern
                            </button>
                        </div>
                    </form>
    </div>

    @push('scripts')
    <script>
        function updateValueLabel() {
            const type = document.getElementById('type').value;
            const label = document.getElementById('value-label');
            label.textContent = type === 'percentage' ? 'Wert (%) *' : 'Wert (€) *';
        }
    </script>
    @endpush
</div>
</x-layouts.app>

