<x-layouts.app>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('organizer.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                                <i class="fas fa-home mr-2"></i>
                                Veranstalter
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rechnungsnummern</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    <i class="fas fa-file-invoice mr-2"></i>Rechnungsnummern für Teilnehmer-Buchungen
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Konfigurieren Sie das Format und die Nummerierung für Rechnungen, die Sie an Ihre Teilnehmer ausstellen.
                </p>
                <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    <strong>Hinweis:</strong> Rechnungen für Plattformgebühren werden von der Plattform verwaltet.
                </p>
            </div>

            <!-- Navigation -->
            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('organizer.bank-account.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 px-1 pb-4 text-sm font-medium">
                        Kontoverbindung
                    </a>
                    <a href="{{ route('organizer.bank-account.billing-data') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 px-1 pb-4 text-sm font-medium">
                        Rechnungsdaten
                    </a>
                    <a href="{{ route('organizer.settings.invoice.index') }}" class="border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 px-1 pb-4 text-sm font-medium">
                        <i class="fas fa-file-invoice mr-1"></i>Rechnungsnummern
                    </a>
                </nav>
            </div>

            <!-- Flash Messages -->
            @if(session('status'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-xl mr-3"></i>
                        <p class="font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                        <p class="font-medium">Fehler aufgetreten:</p>
                    </div>
                    <ul class="list-disc list-inside ml-8">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('organizer.settings.invoice.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Placeholders Info -->
                    <div class="mb-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-3 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Verfügbare Platzhalter
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                            @foreach($placeholders as $placeholder => $description)
                                <div class="flex items-start">
                                    <code class="bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 px-2 py-1 rounded font-mono text-xs mr-2">{{ $placeholder }}</code>
                                    <span class="text-blue-800 dark:text-blue-200">{{ $description }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Booking Invoice Format -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center border-b pb-2">
                            <i class="fas fa-receipt mr-2 text-blue-600"></i>
                            Rechnungen an Teilnehmer
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Diese Rechnungen stellen Sie an Teilnehmer aus, die sich für Ihre Veranstaltungen anmelden.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label for="invoice_number_format_booking" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Format für Rechnungsnummern
                                </label>
                                <input type="text"
                                       name="invoice_number_format_booking"
                                       id="invoice_number_format_booking"
                                       value="{{ old('invoice_number_format_booking', $settings['invoice_number_format_booking']) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                       required>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Beispiel: RE-{YEAR}-{NUMBER} → <span id="preview-booking" class="font-mono font-bold">...</span>
                                </p>
                            </div>

                            <div>
                                <label for="invoice_number_counter_booking" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Aktueller Zählerstand
                                </label>
                                <input type="number"
                                       name="invoice_number_counter_booking"
                                       id="invoice_number_counter_booking"
                                       value="{{ old('invoice_number_counter_booking', $settings['invoice_number_counter_booking']) }}"
                                       min="1"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                       required>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Die nächste Rechnungsnummer wird ab diesem Wert vergeben.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- General Settings -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center border-b pb-2">
                            <i class="fas fa-cog mr-2 text-purple-600"></i>
                            Allgemeine Einstellungen
                        </h3>

                        <div class="space-y-4">
                            <div>
                                <label for="invoice_number_padding" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nullen-Auffüllung für {NUMBER}
                                </label>
                                <input type="number"
                                       name="invoice_number_padding"
                                       id="invoice_number_padding"
                                       value="{{ old('invoice_number_padding', $settings['invoice_number_padding']) }}"
                                       min="1"
                                       max="10"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                       required>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Anzahl der Stellen: 5 → 00001, 00002, ...
                                </p>
                            </div>

                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="invoice_reset_yearly" value="0">
                                    <input type="checkbox"
                                           name="invoice_reset_yearly"
                                           id="invoice_reset_yearly"
                                           value="1"
                                           {{ old('invoice_reset_yearly', $settings['invoice_reset_yearly']) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                                </div>
                                <div class="ml-3">
                                    <label for="invoice_reset_yearly" class="font-medium text-gray-700 dark:text-gray-300">
                                        Zähler jährlich zurücksetzen
                                    </label>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Wenn aktiviert, wird der Zähler am 1. Januar automatisch auf 1 zurückgesetzt.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-600 dark:text-yellow-400 text-xl mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-yellow-900 dark:text-yellow-100 mb-1">Wichtiger Hinweis</h4>
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Diese Einstellungen gelten nur für Ihre Rechnungen. Jeder Veranstalter hat seinen eigenen Rechnungskreis mit individuellen fortlaufenden Nummern.
                                    Ändern Sie die Zählerstände nur, wenn Sie genau wissen, was Sie tun, da dies zu doppelten Rechnungsnummern führen kann.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end pt-6 border-t">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all">
                            <i class="fas fa-save mr-2"></i>
                            Einstellungen speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Live preview of invoice number format
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
                    previewSpan.className = 'font-mono font-bold text-green-600';
                } else {
                    previewSpan.textContent = 'Ungültig!';
                    previewSpan.className = 'font-mono font-bold text-red-600';
                }
            })
            .catch(() => {
                previewSpan.textContent = 'Fehler';
                previewSpan.className = 'font-mono font-bold text-red-600';
            });
        }

        // Update preview on input change
        document.addEventListener('DOMContentLoaded', function() {
            updatePreview();

            document.getElementById('invoice_number_format_booking').addEventListener('input', updatePreview);
            document.getElementById('invoice_number_padding').addEventListener('input', updatePreview);
        });
    </script>
    @endpush
</x-layouts.app>

