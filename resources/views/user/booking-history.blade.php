<x-layouts.app title="Meine Buchungen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Meine Buchungen</h1>
                <p class="text-gray-600 mt-2">Übersicht aller Ihrer Fortbildungs-Buchungen</p>
            </div>

            @if($bookings->count() > 0)
                <!-- Bookings List -->
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <!-- Event Info -->
                                <div class="flex-1">
                                    <div class="flex items-start gap-4">
                                        @if($booking->event->featured_image)
                                            <img src="{{ Storage::url($booking->event->featured_image) }}"
                                                 alt="{{ $booking->event->title }}"
                                                 class="w-24 h-24 object-cover rounded-lg">
                                        @else
                                            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                                <x-icon.academic class="w-12 h-12 text-white opacity-50" />
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <h3 class="text-xl font-bold text-gray-900">{{ $booking->event->title }}</h3>
                                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                                    {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-800' :
                                                       ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                                       ($booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </div>

                                            <div class="space-y-1 text-sm text-gray-600">
                                                <div class="flex items-center">
                                                    <x-icon.calendar class="w-4 h-4 mr-2" />
                                                    {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr
                                                </div>
                                                <div class="flex items-center">
                                                    <x-icon.location class="w-4 h-4 mr-2" />
                                                    {{ $booking->event->venue_city }}
                                                </div>
                                                <div class="flex items-center">
                                                    <x-icon.ticket class="w-4 h-4 mr-2" />
                                                    {{ $booking->items->sum('quantity') }} Ticket(s)
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <span class="text-sm text-gray-500">Buchungsnummer:</span>
                                                <span class="text-sm font-mono font-semibold text-gray-900">{{ $booking->booking_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions & Price -->
                                <div class="flex flex-col items-end gap-3 lg:min-w-[200px]">
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-900">
                                            {{ number_format($booking->total, 2, ',', '.') }} €
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Gebucht am {{ $booking->created_at->format('d.m.Y') }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="px-2 py-1 text-xs font-medium rounded
                                                {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                                                   ($booking->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($booking->payment_status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 justify-end">
                                        <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                            Details
                                        </a>

                                        @if($booking->status === 'confirmed')
                                            <a href="{{ route('bookings.ticket', $booking->booking_number) }}"
                                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                                Ticket
                                            </a>
                                        @endif

                                        <a href="{{ route('bookings.invoice', $booking->booking_number) }}"
                                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                            Rechnung
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <x-icon.ticket class="w-24 h-24 text-gray-300 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Noch keine Buchungen</h2>
                    <p class="text-gray-600 mb-6">
                        Entdecken Sie spannende Fortbildungen und sichern Sie sich Ihren Platz
                    </p>
                    <a href="{{ route('events.index') }}"
                       class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Fortbildungen entdecken
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>

