<x-layouts.app title="Rechnungsverwaltung">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Rechnungsverwaltung</h1>
        <a href="{{ route('admin.invoices.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            📥 CSV Export
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Gesamt Rechnungen</div>
            <div class="text-3xl font-bold text-gray-900">{{ $stats['total_count'] }}</div>
            <div class="text-sm text-gray-500 mt-2">€{{ number_format($stats['total_amount'], 2, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Platform-Fees</div>
            <div class="text-3xl font-bold text-blue-600">€{{ number_format($stats['platform_fee_total'], 2, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Bezahlt</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['paid_count'] }}</div>
            <div class="text-sm text-gray-500 mt-2">€{{ number_format($stats['paid_amount'], 2, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Ausstehend</div>
            <div class="text-3xl font-bold text-orange-600">{{ $stats['pending_count'] }}</div>
            <div class="text-sm text-gray-500 mt-2">€{{ number_format($stats['pending_amount'], 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Typ</label>
                <select name="type" class="w-full border-gray-300 rounded">
                    <option value="">Alle</option>
                    <option value="platform_fee" {{ request('type') === 'platform_fee' ? 'selected' : '' }}>Platform-Fee</option>
                    <option value="participant" {{ request('type') === 'participant' ? 'selected' : '' }}>Teilnehmer</option>
                </select>
            </div>
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
            <input type="text" name="search" placeholder="Suche nach Rechnungsnummer, Name oder E-Mail..."
                   value="{{ request('search') }}"
                   class="w-full border-gray-300 rounded"
                   form="search-form">
            <form id="search-form" method="GET" action="{{ route('admin.invoices.index') }}" class="hidden">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <input type="hidden" name="status" value="{{ request('status') }}">
            </form>
        </div>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rechnung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Typ</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Datum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empfänger</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Betrag</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $invoice)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $invoice->invoice_number }}
                        </a>
                        <div class="text-xs text-gray-500">{{ $invoice->invoice_date->format('d.m.Y') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($invoice->type === 'platform_fee')
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Platform-Fee</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">Teilnehmer</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ $invoice->invoice_date->format('d.m.Y') }}
                        <div class="text-xs text-gray-500">Fällig: {{ $invoice->due_date->format('d.m.Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="font-medium">{{ $invoice->recipient_name }}</div>
                        <div class="text-xs text-gray-500">{{ $invoice->recipient_email }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($invoice->event)
                            <a href="{{ route('events.show', $invoice->event->slug) }}" class="text-blue-600 hover:underline">
                                {{ Str::limit($invoice->event->title, 30) }}
                            </a>
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        €{{ number_format($invoice->total_amount, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($invoice->status === 'paid')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Bezahlt</span>
                        @elseif($invoice->status === 'overdue')
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Überfällig</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Offen</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                        <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">Ansehen</a>
                        <a href="{{ route('admin.invoices.download', $invoice) }}" class="text-green-600 hover:text-green-900">PDF</a>
                        @if($invoice->status !== 'paid')
                            <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-purple-600 hover:text-purple-900">Als bezahlt</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <div class="text-4xl mb-4">📄</div>
                        <div class="text-lg">Keine Rechnungen vorhanden</div>
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

<script>
// Auto-submit search form on input
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => {
        document.getElementById('search-form').submit();
    }, 500);
});
</script>
</x-layouts.app>
