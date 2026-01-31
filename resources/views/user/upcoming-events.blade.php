<x-layouts.app title="Anstehende Veranstaltungen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Anstehende Veranstaltungen</h1>
                <p class="text-gray-600 mt-2">Ihre bevorstehenden Fortbildungen</p>
            </div>

            @if($bookings->count() > 0)
                <!-- Events Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition group">
                            @if($booking->event->featured_image)
                                <div class="h-48 overflow-hidden">
                                    <img src="{{ Storage::url($booking->event->featured_image) }}"
                                         alt="{{ $booking->event->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                </div>
                            @else
                                <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                    <x-icon.academic class="w-20 h-20 text-white opacity-50" />
                                </div>
                            @endif

                            <div class="p-6">
                                <!-- Status Badge -->
                                <div class="mb-3">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Bestätigt
                                    </span>
                                </div>

                                <h3 class="font-bold text-lg text-gray-900 mb-2 line-clamp-2">
                                    {{ $booking->event->title }}
                                </h3>

                                <!-- Event Info -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <x-icon.calendar class="w-4 h-4 mr-2" />
                                        @if($booking->event->start_date->isSameDay($booking->event->end_date))
                                            {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr
                                        @else
                                            {{ $booking->event->start_date->format('d.m.Y') }} - {{ $booking->event->end_date->format('d.m.Y') }}
                                        @endif
                                    </div>

                                    <div class="flex items-center text-sm text-gray-600">
                                        <x-icon.location class="w-4 h-4 mr-2" />
                                        {{ $booking->event->venue_city }}
                                    </div>

                                    <div class="flex items-center text-sm text-gray-600">
                                        <x-icon.ticket class="w-4 h-4 mr-2" />
                                        {{ $booking->items->sum('quantity') }} Ticket(s)
                                    </div>

                                    <!-- Countdown -->
                                    @php
                                        $daysUntil = now()->diffInDays($booking->event->start_date, false);
                                    @endphp
                                    @if($daysUntil >= 0)
                                        <div class="flex items-center text-sm font-medium text-blue-600">
                                            <x-icon.clock class="w-4 h-4 mr-2" />
                                            @if($daysUntil === 0)
                                                Heute!
                                            @elseif($daysUntil === 1)
                                                Morgen
                                            @else
                                                In {{ $daysUntil }} Tagen
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                       class="flex-1 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                        Details
                                    </a>

                                    <a href="{{ route('bookings.ticket', $booking->booking_number) }}"
                                       class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                                        <x-icon.download class="w-5 h-5" />
                                    </a>
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
                    <x-icon.calendar class="w-24 h-24 text-gray-300 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Keine anstehenden Veranstaltungen</h2>
                    <p class="text-gray-600 mb-6">
                        Buchen Sie jetzt Ihre nächste Fortbildung
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

