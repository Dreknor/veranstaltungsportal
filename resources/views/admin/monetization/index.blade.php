<x-layouts.app title="Monetarisierungs-Einstellungen">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Monetarisierungs-Einstellungen</h1>
                <p class="text-gray-600 mt-2">Verwalten Sie Plattformgebühren und Rechnungseinstellungen</p>
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

            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('admin.monetization.index') }}" class="border-b-2 border-blue-500 text-blue-600 px-1 pb-4 text-sm font-medium">
                        Gebühren-Einstellungen
                    </a>
                    <a href="{{ route('admin.monetization.billing-data') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Plattform-Rechnungsdaten
                    </a>
                    <a href="{{ route('admin.settings.invoice.index') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        <i class="fas fa-file-invoice mr-1"></i>Rechnungsnummern
                    </a>
                    @if($featuredStats['total_featured_fees'] > 0 || $settings['featured_event_enabled'])
                    <a href="{{ route('admin.monetization.featured-events') }}" class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 pb-4 text-sm font-medium">
                        Featured Events Übersicht
                        @if($featuredStats['pending_payments'] > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $featuredStats['pending_payments'] }}
                        </span>
                        @endif
                    </a>
                    @endif
                </nav>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('admin.monetization.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Gebührentyp</label>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="radio" name="platform_fee_type" id="type_percentage" value="percentage"
                                           {{ old('platform_fee_type', $settings['platform_fee_type']) == 'percentage' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <label for="type_percentage" class="ml-3 block text-sm text-gray-700">
                                        Prozentual (Empfohlen)
                                    </label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" name="platform_fee_type" id="type_fixed" value="fixed"
                                           {{ old('platform_fee_type', $settings['platform_fee_type']) == 'fixed' ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <label for="type_fixed" class="ml-3 block text-sm text-gray-700">
                                        Festbetrag
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div id="percentage_field">
                            <label for="platform_fee_percentage" class="block text-sm font-medium text-gray-700">
                                Plattformgebühr (Prozent) *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="platform_fee_percentage" id="platform_fee_percentage"
                                       step="0.01" min="0" max="100"
                                       value="{{ old('platform_fee_percentage', $settings['platform_fee_percentage']) }}"
                                       class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Beispiel: Bei 5% und 100€ Buchung werden 5€ Gebühr berechnet
                            </p>
                        </div>

                        <div id="fixed_field" style="display: none;">
                            <label for="platform_fee_fixed_amount" class="block text-sm font-medium text-gray-700">
                                Plattformgebühr (Festbetrag)
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="platform_fee_fixed_amount" id="platform_fee_fixed_amount"
                                       step="0.01" min="0"
                                       value="{{ old('platform_fee_fixed_amount', $settings['platform_fee_fixed_amount']) }}"
                                       class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">€</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Fester Betrag pro Buchung, unabhängig vom Ticket-Preis
                            </p>
                        </div>

                        <div>
                            <label for="platform_fee_minimum" class="block text-sm font-medium text-gray-700">
                                Mindestgebühr pro Buchung *
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" name="platform_fee_minimum" id="platform_fee_minimum"
                                       step="0.01" min="0"
                                       value="{{ old('platform_fee_minimum', $settings['platform_fee_minimum']) }}"
                                       class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">€</span>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Mindestgebühr pro Buchung - wichtig für kostenlose Tickets. Die höhere von berechneter Gebühr oder Mindestgebühr wird verwendet.
                            </p>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rechnungseinstellungen</h3>

                            <div class="flex items-start mb-6">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="auto_invoice" id="auto_invoice" value="1"
                                           {{ old('auto_invoice', $settings['auto_invoice']) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="auto_invoice" class="font-medium text-gray-700">Automatische Rechnungserstellung</label>
                                    <p class="text-gray-500">Rechnungen werden automatisch nach Event-Ende erstellt</p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="invoice_cc_email" class="block text-sm font-medium text-gray-700">
                                    Buchhaltungs-E-Mail (CC)
                                </label>
                                <input type="email" name="invoice_cc_email" id="invoice_cc_email"
                                       value="{{ old('invoice_cc_email', $settings['invoice_cc_email']) }}"
                                       placeholder="buchhaltung@ihre-plattform.de"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-2 text-sm text-gray-500">
                                    Diese E-Mail-Adresse erhält eine Kopie aller generierten Rechnungen
                                </p>
                            </div>

                            <div>
                                <label for="payment_deadline_days" class="block text-sm font-medium text-gray-700">
                                    Zahlungsfrist (Tage) *
                                </label>
                                <input type="number" name="payment_deadline_days" id="payment_deadline_days"
                                       min="1" max="90"
                                       value="{{ old('payment_deadline_days', $settings['payment_deadline_days']) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-2 text-sm text-gray-500">
                                    Anzahl der Tage, die Organisatoren Zeit haben, die Plattformgebühr zu zahlen
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Featured Events Monetarisierung</h3>

                            <div class="flex items-start mb-6">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="featured_event_enabled" id="featured_event_enabled" value="1"
                                           {{ old('featured_event_enabled', $settings['featured_event_enabled']) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="featured_event_enabled" class="font-medium text-gray-700">Featured Events aktivieren</label>
                                    <p class="text-gray-500">Veranstalter können Events gegen Gebühr als "Featured" markieren lassen</p>
                                </div>
                            </div>

                            <div id="featured_settings" class="space-y-6">
                                <!-- Statistics -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg">
                                    <div>
                                        <div class="text-sm text-gray-600">Aktive Featured Events</div>
                                        <div class="text-2xl font-bold text-purple-600">{{ $featuredStats['active_featured_events'] }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Gesamt-Umsatz</div>
                                        <div class="text-2xl font-bold text-green-600">{{ number_format($featuredStats['total_revenue'], 2, ',', '.') }} €</div>
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-600">Umsatz diesen Monat</div>
                                        <div class="text-2xl font-bold text-blue-600">{{ number_format($featuredStats['this_month_revenue'], 2, ',', '.') }} €</div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label for="featured_event_daily_rate" class="block text-sm font-medium text-gray-700">
                                            Tagespreis *
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="featured_event_daily_rate" id="featured_event_daily_rate"
                                                   step="0.01" min="0"
                                                   value="{{ old('featured_event_daily_rate', $settings['featured_event_daily_rate']) }}"
                                                   class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">€</span>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Gebühr für 1 Tag Featured</p>
                                    </div>

                                    <div>
                                        <label for="featured_event_weekly_rate" class="block text-sm font-medium text-gray-700">
                                            Wochenpreis *
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="featured_event_weekly_rate" id="featured_event_weekly_rate"
                                                   step="0.01" min="0"
                                                   value="{{ old('featured_event_weekly_rate', $settings['featured_event_weekly_rate']) }}"
                                                   class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">€</span>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Gebühr für 7 Tage Featured</p>
                                    </div>

                                    <div>
                                        <label for="featured_event_monthly_rate" class="block text-sm font-medium text-gray-700">
                                            Monatspreis *
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <input type="number" name="featured_event_monthly_rate" id="featured_event_monthly_rate"
                                                   step="0.01" min="0"
                                                   value="{{ old('featured_event_monthly_rate', $settings['featured_event_monthly_rate']) }}"
                                                   class="block w-full pr-12 rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">€</span>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">Gebühr für 30 Tage Featured</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="featured_event_max_duration_days" class="block text-sm font-medium text-gray-700">
                                            Maximale Dauer (Tage) *
                                        </label>
                                        <input type="number" name="featured_event_max_duration_days" id="featured_event_max_duration_days"
                                               min="1" max="365"
                                               value="{{ old('featured_event_max_duration_days', $settings['featured_event_max_duration_days']) }}"
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="mt-1 text-xs text-gray-500">
                                            Maximale Anzahl Tage, die ein Event Featured sein kann
                                        </p>
                                    </div>

                                    <div class="flex items-center h-full">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5 mt-1">
                                                <input type="checkbox" name="featured_event_auto_disable_on_expiry" id="featured_event_auto_disable_on_expiry" value="1"
                                                       {{ old('featured_event_auto_disable_on_expiry', $settings['featured_event_auto_disable_on_expiry']) ? 'checked' : '' }}
                                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="featured_event_auto_disable_on_expiry" class="font-medium text-gray-700">Automatisch deaktivieren</label>
                                                <p class="text-gray-500">Featured Status automatisch nach Ablauf entfernen</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Featured Events Overview -->
                                @if($featuredStats['pending_payments'] > 0 || $featuredStats['total_featured_fees'] > 0)
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800">Featured Events Status</h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <ul class="list-disc list-inside space-y-1">
                                                    @if($featuredStats['pending_payments'] > 0)
                                                    <li>{{ $featuredStats['pending_payments'] }} Zahlung(en) ausstehend</li>
                                                    @endif
                                                    <li>{{ $featuredStats['total_featured_fees'] }} bezahlte Featured-Perioden insgesamt</li>
                                                    <li>Nächste automatische Prüfung: Täglich um 00:00 Uhr</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
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
                                <li>Änderungen an Plattformgebühren gelten für alle neuen Buchungen</li>
                                <li>Featured Events Gebühren gelten für neue Featured-Anträge</li>
                                <li>Bestehende Rechnungen bleiben unverändert</li>
                                <li>Die Einstellungen werden in der .env-Datei gespeichert</li>
                                <li>Featured Events werden automatisch deaktiviert wenn diese Option aktiv ist</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organizers with Custom Fees -->
            @if($organizersWithCustomFees->count() > 0)
                <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Organisatoren mit individuellen Gebühren
                            <span class="ml-2 text-sm font-normal text-gray-500">({{ $organizersWithCustomFees->count() }})</span>
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Diese Organisatoren haben Sonderkonditionen, die von den globalen Einstellungen abweichen
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Organisator
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gebührentyp
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gebühr
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notizen
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Letzte Änderung
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aktionen
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($organizersWithCustomFees as $organizer)
                                    @php
                                        $customFee = $organizer->custom_platform_fee;
                                        $feeType = $customFee['fee_type'] ?? 'percentage';
                                        $feeValue = $feeType === 'percentage'
                                            ? ($customFee['fee_percentage'] ?? 0) . '%'
                                            : number_format($customFee['fee_fixed_amount'] ?? 0, 2, ',', '.') . ' €';
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $organizer->name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $organizer->email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $feeType === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $feeType === 'percentage' ? 'Prozentual' : 'Festbetrag' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $feeValue }}
                                            </div>
                                            @if($feeType === 'percentage')
                                                <div class="text-xs text-gray-500">
                                                    Statt {{ $settings['platform_fee_percentage'] }}%
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $customFee['notes'] ?? '' }}">
                                                {{ $customFee['notes'] ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if(isset($customFee['updated_at']))
                                                {{ \Carbon\Carbon::parse($customFee['updated_at'])->format('d.m.Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.organizer-fees.edit', $organizer) }}"
                                               class="text-blue-600 hover:text-blue-900">
                                                Bearbeiten
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Keine individuellen Gebühren</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Derzeit haben keine Organisatoren individuelle Gebühren-Regelungen.
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            Sie können individuelle Gebühren in der Benutzerverwaltung festlegen.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const percentageRadio = document.getElementById('type_percentage');
        const fixedRadio = document.getElementById('type_fixed');
        const percentageField = document.getElementById('percentage_field');
        const fixedField = document.getElementById('fixed_field');

        function toggleFeeFields() {
            if (percentageRadio.checked) {
                percentageField.style.display = 'block';
                fixedField.style.display = 'none';
            } else {
                percentageField.style.display = 'none';
                fixedField.style.display = 'block';
            }
        }

        percentageRadio.addEventListener('change', toggleFeeFields);
        fixedRadio.addEventListener('change', toggleFeeFields);

        toggleFeeFields();

        // Featured Events Toggle
        const featuredEnabledCheckbox = document.getElementById('featured_event_enabled');
        const featuredSettingsDiv = document.getElementById('featured_settings');

        function toggleFeaturedSettings() {
            if (featuredEnabledCheckbox.checked) {
                featuredSettingsDiv.style.display = 'block';
            } else {
                featuredSettingsDiv.style.display = 'none';
            }
        }

        if (featuredEnabledCheckbox) {
            featuredEnabledCheckbox.addEventListener('change', toggleFeaturedSettings);
            toggleFeaturedSettings();
        }

        // Scroll to Featured Events section
        function scrollToFeatured(event) {
            event.preventDefault();
            // Scroll to the featured events checkbox area
            const featuredCheckbox = document.getElementById('featured_event_enabled');
            if (featuredCheckbox) {
                featuredCheckbox.closest('.border-t').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    </script>
    @endpush
</x-layouts.app>

