<x-layouts.app title="Auszahlungseinstellungen">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Auszahlungseinstellungen</h1>
                <p class="text-gray-600 mt-2">Hinterlegen Sie Ihre Bankdaten für monatliche Auszahlungen</p>
            </div>

            <!-- Navigation Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('organizer.monetization.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Übersicht
                    </a>
                    <a href="{{ route('organizer.monetization.earnings') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Einnahmen nach Event
                    </a>
                    <a href="{{ route('organizer.monetization.transactions') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Transaktionen
                    </a>
                    <a href="{{ route('organizer.monetization.payout-settings') }}" class="border-b-2 border-blue-500 text-blue-600 px-1 pb-4 text-sm font-medium">
                        Auszahlungseinstellungen
                    </a>
                </nav>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Bank Details Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('organizer.monetization.payout-settings.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Bankverbindung</h3>

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="bank_name" class="block text-sm font-medium text-gray-700">Bankname</label>
                                    <input type="text" name="bank_name" id="bank_name"
                                           value="{{ old('bank_name', $user->payout_settings['bank_name'] ?? '') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('bank_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="bank_account_holder" class="block text-sm font-medium text-gray-700">Kontoinhaber</label>
                                    <input type="text" name="bank_account_holder" id="bank_account_holder"
                                           value="{{ old('bank_account_holder', $user->payout_settings['bank_account_holder'] ?? '') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('bank_account_holder')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="iban" class="block text-sm font-medium text-gray-700">IBAN</label>
                                    <input type="text" name="iban" id="iban"
                                           value="{{ old('iban', $user->payout_settings['iban'] ?? '') }}"
                                           placeholder="DE89 3704 0044 0532 0130 00"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('iban')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="bic" class="block text-sm font-medium text-gray-700">BIC/SWIFT</label>
                                    <input type="text" name="bic" id="bic"
                                           value="{{ old('bic', $user->payout_settings['bic'] ?? '') }}"
                                           placeholder="COBADEFFXXX"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('bic')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Alternative: PayPal</h3>

                            <div>
                                <label for="paypal_email" class="block text-sm font-medium text-gray-700">PayPal E-Mail</label>
                                <input type="email" name="paypal_email" id="paypal_email"
                                       value="{{ old('paypal_email', $user->payout_settings['paypal_email'] ?? '') }}"
                                       placeholder="ihr-paypal@beispiel.de"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('paypal_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">Optional: Hinterlegen Sie Ihre PayPal E-Mail für schnellere Auszahlungen.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Einstellungen speichern
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
                        <h3 class="text-sm font-medium text-blue-800">Wichtige Hinweise</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Auszahlungen erfolgen monatlich zum 15. des Folgemonats</li>
                                <li>Mindestbetrag für Auszahlungen: 50,00 €</li>
                                <li>Ihre Daten werden verschlüsselt gespeichert</li>
                                <li>Bei Fragen kontaktieren Sie bitte unseren Support</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
<x-layouts.app title="Einnahmen nach Event">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Einnahmen nach Event</h1>
                <p class="text-gray-600 mt-2">Detaillierte Übersicht Ihrer Einnahmen pro Event</p>
            </div>

            <!-- Navigation Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('organizer.monetization.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Übersicht
                    </a>
                    <a href="{{ route('organizer.monetization.earnings') }}" class="border-b-2 border-blue-500 text-blue-600 px-1 pb-4 text-sm font-medium">
                        Einnahmen nach Event
                    </a>
                    <a href="{{ route('organizer.monetization.transactions') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Transaktionen
                    </a>
                    <a href="{{ route('organizer.monetization.payout-settings') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Auszahlungseinstellungen
                    </a>
                </nav>
            </div>

            <!-- Events Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buchungen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bruttoeinnahmen</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plattformgebühr</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nettoeinnahmen</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($eventsWithEarnings as $eventData)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $eventData['event']->title }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $eventData['event']->start_date->format('d.m.Y H:i') }}
                                                </div>
                                                @if($eventData['event']->is_cancelled)
                                                    <span class="inline-flex mt-1 items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Storniert
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $eventData['booking_count'] }}</div>
                                        <div class="text-xs text-gray-500">Bestätigte Buchungen</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ number_format($eventData['revenue'], 2, ',', '.') }} €
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-red-600">
                                            -{{ number_format($eventData['platform_fee'], 2, ',', '.') }} €
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-green-600">
                                            {{ number_format($eventData['net_earnings'], 2, ',', '.') }} €
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('organizer.events.edit', $eventData['event']) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                            Details
                                        </a>
                                        <a href="{{ route('organizer.statistics.event', $eventData['event']) }}" class="text-indigo-600 hover:text-indigo-900">
                                            Statistiken
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-2">Noch keine Einnahmen vorhanden</p>
                                        <a href="{{ route('organizer.events.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                            Erstes Event erstellen
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($eventsWithEarnings->count() > 0)
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900" colspan="2">Gesamt</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">
                                            {{ number_format($eventsWithEarnings->sum('revenue'), 2, ',', '.') }} €
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-red-600">
                                            -{{ number_format($eventsWithEarnings->sum('platform_fee'), 2, ',', '.') }} €
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-green-600">
                                            {{ number_format($eventsWithEarnings->sum('net_earnings'), 2, ',', '.') }} €
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

