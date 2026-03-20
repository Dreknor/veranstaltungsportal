<x-layouts.app title="Rechnungsdaten Export">
<div class="container mx-auto px-4 py-8 max-w-7xl">
    <!-- Header -->
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Rechnungsdaten</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Übersicht der Buchungsdaten für externe Fakturierung</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('organizer.billing-data.export', request()->only('filter')) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                CSV exportieren
            </a>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('status'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('status') }}</p>
        </div>
    @endif

    @if(!$organization->hasExternalInvoicing())
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                ⚠️ Ihr Rechnungsmodus ist auf <strong>Automatisch</strong> gestellt. Diese Seite ist für Organisationen mit externer Rechnungsstellung gedacht.
                <a href="{{ route('organizer.settings.invoice.index') }}" class="underline ml-1">Rechnungsmodus ändern</a>
            </p>
        </div>
    @endif

    <!-- Filter & Suche -->
    <form method="GET" class="mb-6 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Suche nach Buchungsnr., Name, E-Mail..."
               class="flex-1 min-w-64 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">

        <select name="filter" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <option value="" {{ !request('filter') ? 'selected' : '' }}>Alle Buchungen</option>
            <option value="pending" {{ request('filter') === 'pending' ? 'selected' : '' }}>Nicht fakturiert</option>
            <option value="invoiced" {{ request('filter') === 'invoiced' ? 'selected' : '' }}>Fakturiert</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            Filtern
        </button>
        @if(request()->hasAny(['search', 'filter']))
            <a href="{{ route('organizer.billing-data.index') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Zurücksetzen
            </a>
        @endif
    </form>

    <!-- Bulk-Aktion -->
    <form method="POST" action="{{ route('organizer.billing-data.bulk-mark-invoiced') }}" id="bulk-form">
        @csrf
        @method('PUT')

    <!-- Tabelle -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $bookings->total() }} Buchung(en)</span>
            <button type="submit" form="bulk-form"
                    class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                Auswahl als fakturiert markieren
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Buchungsnr.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Veranstaltung</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kunde</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Firma</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Betrag</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Datum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ext. Rechnungsnr.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aktion</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}"
                                   class="booking-checkbox rounded border-gray-300 text-blue-600">
                        </td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">
                            {{ $booking->booking_number }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate">
                            {{ $booking->event->title ?? '–' }}
                            <div class="text-xs text-gray-500">{{ $booking->event->start_date?->format('d.m.Y') }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            {{ $booking->customer_name }}
                            <div class="text-xs text-gray-500">{{ $booking->customer_email }}</div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $booking->billing_company ?: '–' }}
                            @if($booking->billing_vat_id)
                                <div class="text-xs">USt: {{ $booking->billing_vat_id }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ format_currency($booking->total) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                            {{ $booking->created_at->format('d.m.Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($booking->externally_invoiced)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    ✓ Fakturiert
                                </span>
                                @if($booking->externally_invoiced_at)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $booking->externally_invoiced_at->format('d.m.Y') }}</div>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                    Ausstehend
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 font-mono">
                            {{ $booking->external_invoice_number ?: '–' }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if(!$booking->externally_invoiced)
                            <form method="POST" action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}"
                                  class="inline" x-data="{ open: false }">
                                @csrf
                                @method('PUT')
                                <div x-show="open" class="mb-2">
                                    <input type="text" name="external_invoice_number"
                                           placeholder="Ext. Rechnungsnr. (optional)"
                                           class="text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 w-40">
                                </div>
                                <button type="button" @click="open = !open"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs" x-show="!open">
                                    Als fakturiert markieren
                                </button>
                                <button type="submit" x-show="open"
                                        class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                    Speichern
                                </button>
                            </form>
                            @else
                                <span class="text-xs text-gray-400">–</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            Keine Buchungen gefunden.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </form>

    <!-- Pagination -->
    @if($bookings->hasPages())
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
document.getElementById('select-all')?.addEventListener('change', function () {
    document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endpush
</x-layouts.app>

