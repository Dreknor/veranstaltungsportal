<x-layouts.app title="Rechnungsnummern">
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rechnungsdaten verwalten</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Verwalten Sie Ihre Bankdaten, Firmendaten und Rechnungseinstellungen</p>
    </div>

    <!-- Alerts -->
    @if(session('status'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('status') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Fehler aufgetreten:</p>
                    <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('organizer.bank-account.index') }}"
               class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 px-1 pb-4 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Kontoverbindung
            </a>
            <a href="{{ route('organizer.bank-account.billing-data') }}"
               class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 px-1 pb-4 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Rechnungsdaten
            </a>
            <a href="{{ route('organizer.settings.invoice.index') }}"
               class="border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 px-1 pb-4 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rechnungsnummern
            </a>
        </nav>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Rechnungsnummern-Format</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
            Konfigurieren Sie das Format und die Nummerierung für Rechnungen an Ihre Teilnehmer
        </p>

        <form action="{{ route('organizer.settings.invoice.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Placeholders Info -->
            <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Verfügbare Platzhalter
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                    @foreach($placeholders as $placeholder => $description)
                        <div class="flex items-start gap-2">
                            <code class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 px-2 py-1 rounded font-mono text-xs whitespace-nowrap">{{ $placeholder }}</code>
                            <span class="text-blue-800 dark:text-blue-200">{{ $description }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Invoice Format -->
            <div class="space-y-4 mb-6">
                <div>
                    <label for="invoice_number_format_booking" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Format für Rechnungsnummern <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="invoice_number_format_booking" id="invoice_number_format_booking"
                           value="{{ old('invoice_number_format_booking', $settings['invoice_number_format_booking']) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 font-mono"
                           placeholder="RE-{YEAR}-{NUMBER}" required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Beispiel: RE-{YEAR}-{NUMBER} → <span id="preview-booking" class="font-mono font-bold text-blue-600 dark:text-blue-400">...</span>
                    </p>
                </div>

                <div>
                    <label for="invoice_number_counter_booking" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Aktueller Zählerstand <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="invoice_number_counter_booking" id="invoice_number_counter_booking"
                           value="{{ old('invoice_number_counter_booking', $settings['invoice_number_counter_booking']) }}"
                           min="1"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                           required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Die nächste Rechnungsnummer wird ab diesem Wert vergeben
                    </p>
                </div>

                <div>
                    <label for="invoice_number_padding" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nullen-Auffüllung für {NUMBER} <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="invoice_number_padding" id="invoice_number_padding"
                           value="{{ old('invoice_number_padding', $settings['invoice_number_padding']) }}"
                           min="1" max="10"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                           required>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Anzahl der Stellen: 5 → 00001, 00002, ...
                    </p>
                </div>

                <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                    <input type="hidden" name="invoice_reset_yearly" value="0">
                    <input type="checkbox" name="invoice_reset_yearly" id="invoice_reset_yearly" value="1"
                           {{ old('invoice_reset_yearly', $settings['invoice_reset_yearly']) ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <div class="flex-1">
                        <label for="invoice_reset_yearly" class="font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Zähler jährlich zurücksetzen
                        </label>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Wenn aktiviert, wird der Zähler am 1. Januar automatisch auf 1 zurückgesetzt
                        </p>
                    </div>
                </div>
            </div>

            <!-- Warning Box -->
            <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-100">Wichtiger Hinweis</h4>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            Diese Einstellungen gelten nur für Ihre Rechnungen. Ändern Sie die Zählerstände nur, wenn Sie genau wissen, was Sie tun, da dies zu doppelten Rechnungsnummern führen kann.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Einstellungen speichern
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updatePreview() {
    const formatInput = document.getElementById('invoice_number_format_booking');
    const paddingInput = document.getElementById('invoice_number_padding');
    const previewSpan = document.getElementById('preview-booking');

    const format = formatInput.value;
    const padding = parseInt(paddingInput.value) || 5;

    fetch('{{ route('organizer.settings.invoice.preview') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ format, padding })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            previewSpan.textContent = data.preview;
            previewSpan.className = 'font-mono font-bold text-green-600 dark:text-green-400';
        } else {
            previewSpan.textContent = 'Ungültig!';
            previewSpan.className = 'font-mono font-bold text-red-600 dark:text-red-400';
        }
    })
    .catch(() => {
        previewSpan.textContent = 'Fehler';
        previewSpan.className = 'font-mono font-bold text-red-600 dark:text-red-400';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const formatInput = document.getElementById('invoice_number_format_booking');
    const paddingInput = document.getElementById('invoice_number_padding');

    if (formatInput && paddingInput) {
        formatInput.addEventListener('input', updatePreview);
        paddingInput.addEventListener('input', updatePreview);
        updatePreview();
    }
});
</script>
@endpush
</x-layouts.app>

