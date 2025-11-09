<x-layouts.app>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('organizer.invoices.index') }}" class="text-blue-600 hover:underline mb-2 inline-block">
                ← Zurück zu Rechnungen
            </a>
            <h1 class="text-3xl font-bold">Rechnung {{ $invoice->invoice_number }}</h1>
        </div>
        <div class="space-x-2">
            <a href="{{ route('organizer.invoices.download', $invoice) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded inline-flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                PDF herunterladen
            </a>
        </div>
    </div>

    {{-- Status Alert --}}
    @if($invoice->status === 'paid')
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium">Rechnung bezahlt</p>
                    <p class="text-sm">Bezahlt am {{ $invoice->paid_at->format('d.m.Y') }}</p>
                </div>
            </div>
        </div>
    @elseif($invoice->due_date->isPast())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium">Rechnung überfällig</p>
                    <p class="text-sm">Fällig seit {{ $invoice->due_date->format('d.m.Y') }}</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="font-medium">Zahlungsziel</p>
                    <p class="text-sm">Fällig am {{ $invoice->due_date->format('d.m.Y') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Invoice Details --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-semibold">Rechnungsdetails</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rechnungsnummer</dt>
                            <dd class="mt-1 text-lg font-semibold">{{ $invoice->invoice_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                @if($invoice->status === 'paid')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Bezahlt</span>
                                @elseif($invoice->status === 'overdue')
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Überfällig</span>
                                @else
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-800">Offen</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rechnungsdatum</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->invoice_date->format('d.m.Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fälligkeitsdatum</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $invoice->due_date->format('d.m.Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Veranstaltung</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($invoice->event)
                                    <a href="{{ route('organizer.events.edit', $invoice->event) }}" class="text-blue-600 hover:underline">
                                        {{ $invoice->event->title }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Event-Datum</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($invoice->event)
                                    {{ $invoice->event->start_date->format('d.m.Y') }}
                                @else
                                    N/A
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Platform Fees Breakdown --}}
            @if(isset($platformFees) && $platformFees->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-semibold">Platform-Fees Aufschlüsselung</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buchung</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buchungsbetrag</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee %</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Fee Betrag</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($platformFees as $fee)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($fee->booking)
                                        <a href="{{ route('organizer.bookings.show', $fee->booking) }}" class="text-blue-600 hover:underline">
                                            {{ $fee->booking->booking_number }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">€{{ number_format($fee->booking_amount, 2, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $fee->fee_percentage }}%</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">€{{ number_format($fee->fee_amount, 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="3" class="px-6 py-4 text-right">Gesamt (netto):</td>
                                <td class="px-6 py-4 text-right">€{{ number_format($platformFees->sum('fee_amount'), 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Billing Data --}}
            @if(isset($invoice->billing_data['items']))
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h2 class="text-xl font-semibold">Rechnungspositionen</h2>
                </div>
                <div class="p-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2">Beschreibung</th>
                                <th class="text-right py-2">Menge</th>
                                <th class="text-right py-2">Einzelpreis</th>
                                <th class="text-right py-2">Gesamt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->billing_data['items'] as $item)
                            <tr class="border-b">
                                <td class="py-3">
                                    {{ $item['description'] ?? 'Platform-Fee' }}
                                    @if(isset($item['details']))
                                        <br><small class="text-gray-600">{{ $item['details'] }}</small>
                                    @endif
                                </td>
                                <td class="text-right py-3">{{ $item['quantity'] ?? 1 }}</td>
                                <td class="text-right py-3">€{{ number_format($item['unit_price'] ?? $item['total'], 2, ',', '.') }}</td>
                                <td class="text-right py-3 font-medium">€{{ number_format($item['total'], 2, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Amount Summary --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Rechnungsbetrag</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-600">Zwischensumme (netto):</dt>
                        <dd class="font-medium">€{{ number_format($invoice->amount, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-600">MwSt. ({{ number_format($invoice->tax_rate, 1) }}%):</dt>
                        <dd class="font-medium">€{{ number_format($invoice->tax_amount, 2, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between pt-2 border-t-2 border-gray-300">
                        <dt class="text-lg font-semibold">Gesamtbetrag (brutto):</dt>
                        <dd class="text-lg font-bold text-blue-600">€{{ number_format($invoice->total_amount, 2, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Payment Info --}}
            @if($invoice->status !== 'paid' && isset($invoice->billing_data['platform']))
                @php $platform = $invoice->billing_data['platform']; @endphp
                <div class="bg-blue-50 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Zahlungsinformationen</h3>
                    <dl class="space-y-2 text-sm">
                        @if(!empty($platform['bank_name']))
                        <div>
                            <dt class="text-gray-600">Bank:</dt>
                            <dd class="font-medium">{{ $platform['bank_name'] }}</dd>
                        </div>
                        @endif
                        @if(!empty($platform['iban']))
                        <div>
                            <dt class="text-gray-600">IBAN:</dt>
                            <dd class="font-mono font-medium">{{ $platform['iban'] }}</dd>
                        </div>
                        @endif
                        @if(!empty($platform['bic']))
                        <div>
                            <dt class="text-gray-600">BIC:</dt>
                            <dd class="font-medium">{{ $platform['bic'] }}</dd>
                        </div>
                        @endif
                        <div class="pt-2 border-t">
                            <dt class="text-gray-600">Verwendungszweck:</dt>
                            <dd class="font-medium">{{ $invoice->invoice_number }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            {{-- Recipient Info --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Rechnungsempfänger</h3>
                <div class="text-sm">
                    <p class="font-medium">{{ $invoice->recipient_name }}</p>
                    @if($invoice->recipient_address)
                        <p class="text-gray-600 whitespace-pre-line">{{ $invoice->recipient_address }}</p>
                    @endif
                    <p class="text-gray-600 mt-2">{{ $invoice->recipient_email }}</p>
                </div>
            </div>

            {{-- PDF Download --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Dokument</h3>
                @if($invoice->pdf_path)
                    <a href="{{ route('organizer.invoices.download', $invoice) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-3 rounded">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        PDF herunterladen
                    </a>
                @else
                    <p class="text-gray-500 text-sm">PDF wird generiert...</p>
                @endif
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
