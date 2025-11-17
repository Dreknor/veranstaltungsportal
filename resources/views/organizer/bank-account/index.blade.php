<x-layouts.app title="Kontoverbindung">
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rechnungsdaten verwalten</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Verwalten Sie Ihre Bankdaten, Firmendaten und Rechnungseinstellungen</p>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Requirements Warning -->
    @if(!auth()->user()->hasCompleteBankAccount())
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-4 rounded">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-yellow-900 dark:text-yellow-100">Bankverbindung unvollständig</p>
                    <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                        Bitte füllen Sie alle Felder aus, um Events veröffentlichen zu können.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <a href="{{ route('organizer.bank-account.index') }}"
               class="border-b-2 border-blue-500 text-blue-600 dark:text-blue-400 px-1 pb-4 text-sm font-medium flex items-center gap-2">
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
               class="border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 px-1 pb-4 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Rechnungsnummern
            </a>
        </nav>
    </div>

    <!-- Bank Account Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Bankverbindung</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Hinterlegen Sie Ihre Bankdaten für Teilnehmerzahlungen</p>

        <form method="POST" action="{{ route('organizer.bank-account.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="account_holder" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Kontoinhaber <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_holder" id="account_holder" required
                           value="{{ old('account_holder', $bankAccount['account_holder'] ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100"
                           placeholder="Max Mustermann">
                    @error('account_holder')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Bankname <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bank_name" id="bank_name" required
                           value="{{ old('bank_name', $bankAccount['bank_name'] ?? '') }}"
                           placeholder="z.B. Sparkasse München"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                    @error('bank_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="iban" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        IBAN <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="iban" id="iban" required
                           value="{{ old('iban', $bankAccount['iban'] ?? '') }}"
                           placeholder="DE89 3704 0044 0532 0130 00"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 font-mono">
                    @error('iban')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="bic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        BIC/SWIFT <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bic" id="bic" required
                           value="{{ old('bic', $bankAccount['bic'] ?? '') }}"
                           placeholder="COBADEFFXXX"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100 font-mono">
                    @error('bic')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700 mt-6">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Kontoverbindung speichern
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100">Wichtige Informationen</h3>
                <ul class="mt-2 text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
                    <li>Diese Kontoverbindung wird auf den Rechnungen an Ihre Teilnehmer angezeigt</li>
                    <li>Teilnehmer überweisen die Ticketpreise direkt auf dieses Konto</li>
                    <li>Stellen Sie sicher, dass alle Daten korrekt sind</li>
                    <li>Ihre Daten werden sicher und verschlüsselt gespeichert</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>

