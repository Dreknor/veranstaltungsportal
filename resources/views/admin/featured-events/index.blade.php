<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Featured Events Verwaltung
            </h2>
            <a href="{{ route('admin.featured-events.statistics') }}" class="btn-secondary">
                <i class="fas fa-chart-bar mr-2"></i>
                Statistiken
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Gesamt</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktiv</div>
                    <div class="mt-2 text-3xl font-semibold text-green-600 dark:text-green-400">{{ $stats['active'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Ausstehend</div>
                    <div class="mt-2 text-3xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Umsatz</div>
                    <div class="mt-2 text-3xl font-semibold text-blue-600 dark:text-blue-400">{{ number_format($stats['revenue'], 2, ',', '.') }} €</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Offen</div>
                    <div class="mt-2 text-3xl font-semibold text-orange-600 dark:text-orange-400">{{ number_format($stats['pending_revenue'], 2, ',', '.') }} €</div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.featured-events.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Event suchen..." class="input">
                    </div>
                    <div class="min-w-[150px]">
                        <select name="status" class="input">
                            <option value="">Alle Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ausstehend</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Bezahlt</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Fehlgeschlagen</option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Erstattet</option>
                        </select>
                    </div>
                    <div class="min-w-[150px]">
                        <select name="duration" class="input">
                            <option value="">Alle Zeiträume</option>
                            <option value="daily" {{ request('duration') === 'daily' ? 'selected' : '' }}>Täglich</option>
                            <option value="weekly" {{ request('duration') === 'weekly' ? 'selected' : '' }}>Wöchentlich</option>
                            <option value="monthly" {{ request('duration') === 'monthly' ? 'selected' : '' }}>Monatlich</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search mr-2"></i>
                        Filtern
                    </button>
                    @if(request()->hasAny(['search', 'status', 'duration']))
                        <a href="{{ route('admin.featured-events.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>
                            Zurücksetzen
                        </a>
                    @endif
                </form>
            </div>

            <!-- Featured Events Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form id="bulkActionForm" method="POST" action="{{ route('admin.featured-events.bulk') }}">
                        @csrf
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Featured Event Gebühren ({{ $fees->total() }})
                            </h3>
                            <div class="flex gap-2">
                                <select name="action" id="bulkAction" class="input" style="width: auto;">
                                    <option value="">Bulk-Aktion wählen...</option>
                                    <option value="mark_paid">Als bezahlt markieren</option>
                                    <option value="mark_failed">Als fehlgeschlagen markieren</option>
                                    <option value="send_reminder">Erinnerung senden</option>
                                </select>
                                <button type="submit" class="btn-secondary" onclick="return confirm('Sind Sie sicher?')">
                                    Ausführen
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left">
                                            <input type="checkbox" id="selectAll" class="rounded">
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Event</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Veranstalter</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Betrag</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Zeitraum</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Läuft ab</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($fees as $fee)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="fee_ids[]" value="{{ $fee->id }}" class="fee-checkbox rounded">
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::limit($fee->event->title, 40) }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $fee->created_at->format('d.m.Y H:i') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">{{ $fee->event->organizer->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($fee->fee_amount, 2, ',', '.') }} €</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($fee->featured_start_date)->diffInDays(\Carbon\Carbon::parse($fee->featured_end_date)) }} Tag(e)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ \Carbon\Carbon::parse($fee->featured_start_date)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($fee->featured_end_date)->format('d.m.Y') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($fee->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($fee->payment_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                    @elseif($fee->payment_status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                    @endif">
                                                    {{ ucfirst($fee->payment_status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ \Carbon\Carbon::parse($fee->featured_end_date)->format('d.m.Y') }}
                                                </div>
                                                @if(\Carbon\Carbon::parse($fee->featured_end_date)->isPast())
                                                    <span class="text-xs text-red-600 dark:text-red-400">Abgelaufen</span>
                                                @elseif(\Carbon\Carbon::parse($fee->featured_end_date)->diffInDays() <= 3)
                                                    <span class="text-xs text-orange-600 dark:text-orange-400">Läuft bald ab</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('admin.featured-events.show', $fee) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                                    Details
                                                </a>
                                                @if($fee->payment_status === 'pending')
                                                    <form method="POST" action="{{ route('admin.featured-events.send-reminder', $fee) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                                            Erinnern
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                                Keine Featured Event Gebühren gefunden.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>

                @if($fees->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $fees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('selectAll').addEventListener('change', function(e) {
            document.querySelectorAll('.fee-checkbox').forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });
    </script>
    @endpush
</x-layouts.app>

