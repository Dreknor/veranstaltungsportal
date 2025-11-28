<x-layouts.app :title="'Platform-Gebühren Übersicht - ' . config('app.name')" :header="false">
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold">Platform-Gebühren Übersicht</h1>
            <p class="text-gray-600 mt-2">Detaillierte Aufschlüsselung der Platform-Fees für Ihre Events</p>
        </div>
        <a href="{{ route('organizer.invoices.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded">
            ← Zurück zu Rechnungen
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Gesamt Buchungsumsatz</div>
            <div class="text-3xl font-bold text-blue-600">€{{ number_format($totalBookings, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Summe aller bezahlten Buchungen</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-2">Gesamt Platform-Gebühren</div>
            <div class="text-3xl font-bold text-orange-600">€{{ number_format($totalFees, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $platformFees->count() }} Transaktionen</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" action="{{ route('organizer.invoices.platform-fees') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-2">Event</label>
                <select name="event_id" class="w-full border-gray-300 rounded">
                    <option value="">Alle Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }}
                        </option>
                    @endforeach
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
    </div>

    {{-- Platform Fees Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Event
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Event-Datum
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Buchungsumsatz
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Fee-Satz
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Platform-Gebühr
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Berechnet am
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($platformFees as $fee)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        <a href="{{ route('organizer.events.edit', $fee->event) }}" class="text-blue-600 hover:underline">
                            {{ $fee->event->title }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $fee->event->start_date->format('d.m.Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        €{{ number_format($fee->booking_amount, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $fee->fee_percentage }}%
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-orange-600">
                        €{{ number_format($fee->fee_amount, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $fee->created_at->format('d.m.Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="text-4xl mb-4">💰</div>
                        <div class="text-lg">Keine Platform-Gebühren vorhanden</div>
                        <div class="text-sm mt-2">Gebühren werden automatisch nach Event-Ende berechnet.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($platformFees->count() > 0)
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td colspan="2" class="px-6 py-4 text-sm text-gray-900">
                        Gesamt
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        €{{ number_format($totalBookings, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        -
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-orange-600">
                        €{{ number_format($totalFees, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        -
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    {{-- Pagination --}}
    @if($platformFees->hasPages())
    <div class="mt-6">
        {{ $platformFees->links() }}
    </div>
    @endif

    {{-- Info Box --}}
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h3 class="font-semibold text-blue-900 mb-2">ℹ️ Hinweise zu Platform-Gebühren</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Platform-Gebühren werden automatisch nach Event-Ende berechnet</li>
            <li>• Der Fee-Satz kann individuell für Ihre Organisation angepasst sein</li>
            <li>• Rechnungen werden separat erstellt und per E-Mail versandt</li>
            <li>• Bei Fragen zu Gebühren wenden Sie sich bitte an den Support</li>
        </ul>
    </div>
</div>
</x-layouts.app>
