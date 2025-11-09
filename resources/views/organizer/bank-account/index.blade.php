<x-layouts.app title="Kontoverbindung">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Kontoverbindung</h1>
                <p class="text-gray-600 mt-2">Hinterlegen Sie Ihre Bankdaten für Teilnehmerzahlungen</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Navigation -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('organizer.bank-account.index') }}" class="border-b-2 border-blue-500 text-blue-600 px-1 pb-4 text-sm font-medium">
                        Kontoverbindung
                    </a>
                    <a href="{{ route('organizer.bank-account.billing-data') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Rechnungsdaten
                    </a>
                </nav>
            </div>

            <!-- Bank Account Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('organizer.bank-account.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label for="account_holder" class="block text-sm font-medium text-gray-700">Kontoinhaber *</label>
                            <input type="text" name="account_holder" id="account_holder" required
                                   value="{{ old('account_holder', $bankAccount['account_holder'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('account_holder')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700">Bankname *</label>
                            <input type="text" name="bank_name" id="bank_name" required
                                   value="{{ old('bank_name', $bankAccount['bank_name'] ?? '') }}"
                                   placeholder="z.B. Sparkasse München"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('bank_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="iban" class="block text-sm font-medium text-gray-700">IBAN *</label>
                            <input type="text" name="iban" id="iban" required
                                   value="{{ old('iban', $bankAccount['iban'] ?? '') }}"
                                   placeholder="DE89 3704 0044 0532 0130 00"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('iban')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bic" class="block text-sm font-medium text-gray-700">BIC/SWIFT *</label>
                            <input type="text" name="bic" id="bic" required
                                   value="{{ old('bic', $bankAccount['bic'] ?? '') }}"
                                   placeholder="COBADEFFXXX"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('bic')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Kontoverbindung speichern
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Wichtige Informationen</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Diese Kontoverbindung wird auf den Rechnungen an Ihre Teilnehmer angezeigt</li>
                                <li>Teilnehmer überweisen die Ticketpreise direkt auf dieses Konto</li>
                                <li>Stellen Sie sicher, dass alle Daten korrekt sind</li>
                                <li>Ihre Daten werden sicher und verschlüsselt gespeichert</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

