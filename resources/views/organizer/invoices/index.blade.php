<x-layouts.app :title="'Rechnungen - ' . config('app.name')" :header="false">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Platform-Fee Rechnungen</h1>
        <a href="{{ route('organizer.invoices.platform-fees') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            Platform Fees Übersicht
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Gesamt</div>
            <div class="text-3xl font-bold text-gray-900">€{{ number_format($totals['total'], 2, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Bezahlt</div>
            <div class="text-3xl font-bold text-green-600">€{{ number_format($totals['paid'], 2, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Offen</div>
            <div class="text-3xl font-bold text-orange-600">€{{ number_format($totals['pending'], 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('organizer.invoices.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" class="w-full border-gray-300 rounded">
                    <option value="">Alle</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Versendet</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Bezahlt</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Überfällig</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Von Datum</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full border-gray-300 rounded">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Bis Datum</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full border-gray-300 rounded">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Filtern
                </button>
            </div>
        </form>
        <div class="mt-4">
            <a href="{{ route('organizer.invoices.export', ['format' => 'csv']) }}" class="text-blue-600 hover:underline">
                📥 Als CSV exportieren
            </a>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Rechnungsnummer
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Datum
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Event
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Betrag
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fällig am
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aktionen
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('organizer.invoices.show', $invoice) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $invoice->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $invoice->invoice_date->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ $invoice->event->title ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        €{{ number_format($invoice->total_amount, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $invoice->due_date->format('d.m.Y') }}
                        @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                            <span class="text-red-600 text-xs">(überfällig)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($invoice->status === 'paid')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Bezahlt
                            </span>
                        @elseif($invoice->status === 'overdue')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Überfällig
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                Offen
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('organizer.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            Ansehen
                        </a>
                        <a href="{{ route('organizer.invoices.download', $invoice) }}" class="text-green-600 hover:text-green-900">
                            PDF
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <div class="text-4xl mb-4">📄</div>
                        <div class="text-lg">Keine Rechnungen vorhanden</div>
                        <div class="text-sm mt-2">Rechnungen werden automatisch nach Veranstaltungsende erstellt.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div class="mt-6">
        {{ $invoices->links() }}
    </div>
    @endif
</div>
</x-layouts.app>
