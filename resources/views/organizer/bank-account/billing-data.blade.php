<x-layouts.app title="Rechnungsdaten">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Rechnungsdaten</h1>
                <p class="text-gray-600 mt-2">Ihre Firmendaten für Rechnungen an Teilnehmer</p>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Navigation -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('organizer.bank-account.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Kontoverbindung
                    </a>
                    <a href="{{ route('organizer.bank-account.billing-data') }}" class="border-b-2 border-blue-500 text-blue-600 px-1 pb-4 text-sm font-medium">
                        Rechnungsdaten
                    </a>
                </nav>
            </div>

            <!-- Billing Data Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('organizer.bank-account.billing-data.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700">Firmenname / Name *</label>
                            <input type="text" name="company_name" id="company_name" required
                                   value="{{ old('company_name', $billingData['company_name'] ?? '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('company_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_address" class="block text-sm font-medium text-gray-700">Straße & Hausnummer *</label>
                                <input type="text" name="company_address" id="company_address" required
                                       value="{{ old('company_address', $billingData['company_address'] ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('company_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_postal_code" class="block text-sm font-medium text-gray-700">PLZ *</label>
                                <input type="text" name="company_postal_code" id="company_postal_code" required
                                       value="{{ old('company_postal_code', $billingData['company_postal_code'] ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('company_postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_city" class="block text-sm font-medium text-gray-700">Stadt *</label>
                                <input type="text" name="company_city" id="company_city" required
                                       value="{{ old('company_city', $billingData['company_city'] ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('company_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_country" class="block text-sm font-medium text-gray-700">Land *</label>
                                <input type="text" name="company_country" id="company_country" required
                                       value="{{ old('company_country', $billingData['company_country'] ?? 'Deutschland') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('company_country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="tax_id" class="block text-sm font-medium text-gray-700">Steuernummer</label>
                                <input type="text" name="tax_id" id="tax_id"
                                       value="{{ old('tax_id', $billingData['tax_id'] ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('tax_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="vat_id" class="block text-sm font-medium text-gray-700">USt-IdNr.</label>
                                <input type="text" name="vat_id" id="vat_id"
                                       value="{{ old('vat_id', $billingData['vat_id'] ?? '') }}"
                                       placeholder="DE123456789"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('vat_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="company_email" class="block text-sm font-medium text-gray-700">E-Mail *</label>
                                <input type="email" name="company_email" id="company_email" required
                                       value="{{ old('company_email', $billingData['company_email'] ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('company_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="company_phone" class="block text-sm font-medium text-gray-700">Telefon *</label>
                                <input type="text" name="company_phone" id="company_phone" required
                                       value="{{ old('company_phone', $billingData['company_phone'] ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('company_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Rechnungsdaten speichern
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
                        <h3 class="text-sm font-medium text-blue-800">Verwendung der Rechnungsdaten</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Diese Daten werden als Absender auf den Rechnungen an Ihre Teilnehmer angezeigt.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

