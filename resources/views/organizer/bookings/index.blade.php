<x-layouts.app>
    <div class="px-4 py-8">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Event-Buchungen</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    @if($groupByEvent)
                        Übersicht aller Buchungen, nach Veranstaltung gruppiert
                    @else
                        Gefilterte Buchungsliste
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('organizer.bookings.export', array_merge(request()->all(), ['format' => 'csv'])) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    CSV Export
                </a>
                <a href="{{ route('organizer.bookings.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Excel Export
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-6">
            <form action="{{ route('organizer.bookings.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Suche</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               placeholder="Buchungsnummer, Name, E-Mail..."
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="event_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event</label>
                        <select name="event_id" id="event_id"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Alle Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zahlungsstatus</label>
                        <select name="payment_status" id="payment_status"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Alle</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Ausstehend</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Bezahlt</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Fehlgeschlagen</option>
                            <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Erstattet</option>
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buchungsstatus</label>
                        <select name="status" id="status"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Alle</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ausstehend</option>
                            <option value="pending_approval" {{ request('status') === 'pending_approval' ? 'selected' : '' }}>Wartet auf Bestätigung</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Bestätigt</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Abgeschlossen</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Storniert</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Filtern
                        </button>
                        <a href="{{ route('organizer.bookings.index') }}" class="ml-2 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                            Zurücksetzen
                        </a>
                    </div>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- ================================================================
             MODUS 1: Gruppierte Ansicht (kein aktiver Filter)
             ================================================================ --}}
        @if($groupByEvent)
            @forelse($groupedEvents as $groupedEvent)
                <div class="mb-4 bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden"
                     x-data="{ open: false }">
                    {{-- Event-Header --}}
                    <button type="button"
                            @click="open = !open"
                            class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="min-w-0">
                            <div class="text-base font-semibold text-gray-900 dark:text-gray-100 truncate">
                                {{ $groupedEvent->title }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $groupedEvent->start_date->format('d.m.Y') }}
                                @if($groupedEvent->location)
                                    &mdash; {{ $groupedEvent->location }}
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3 ml-4 shrink-0">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $groupedEvent->bookings_count }} Buchung(en)
                            </span>
                            @if($groupedEvent->confirmed_bookings_count > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    ✓ {{ $groupedEvent->confirmed_bookings_count }}
                                </span>
                            @endif
                            @if($groupedEvent->pending_bookings_count > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                    ⏳ {{ $groupedEvent->pending_bookings_count }}
                                </span>
                            @endif
                            <a href="{{ route('organizer.bookings.index', ['event_id' => $groupedEvent->id]) }}"
                               @click.stop
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">
                                Nur dieses Event
                            </a>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                 :class="{ 'rotate-180': open }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </button>

                    {{-- Buchungstabelle (aufgeklappt) --}}
                    <div x-show="open" x-cloak class="border-t border-gray-200 dark:border-gray-700">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Buchungsnr.</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kunde</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tickets</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Betrag</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zahlung</th>
                                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Datum</th>
                                        @if($organization->hasExternalInvoicing())
                                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fakt.</th>
                                        @endif
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aktionen</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($groupedEvent->bookings as $booking)
                                        @include('organizer.bookings._row', ['booking' => $booking, 'organization' => $organization])
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                                Keine Buchungen vorhanden.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Noch keine Buchungen vorhanden.</p>
                </div>
            @endforelse

            @if(isset($groupedEvents) && $groupedEvents->hasPages())
                <div class="mt-6">
                    {{ $groupedEvents->appends(request()->query())->links() }}
                </div>
            @endif

        {{-- ================================================================
             MODUS 2: Gefilterte Flachliste
             ================================================================ --}}
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Buchungsnr.</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Event</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kunde</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tickets</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Betrag</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Zahlung</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-28">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Datum</th>
                            @if($organization->hasExternalInvoicing())
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fakt.</th>
                            @endif
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($bookings as $booking)
                            @include('organizer.bookings._row', ['booking' => $booking, 'organization' => $organization, 'showEvent' => true])
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    Keine Buchungen gefunden.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($bookings) && $bookings->hasPages())
                <div class="mt-6">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
