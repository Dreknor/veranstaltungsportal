<x-layouts.app title="Vergangene Veranstaltungen">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Vergangene Veranstaltungen</h1>
                <p class="text-gray-600 mt-2">Ihre besuchten Fortbildungen</p>
            </div>

            @if($bookings->count() > 0)
                <!-- Events List -->
                <div class="space-y-4">
                    @foreach($bookings as $booking)
                        <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                            <div class="flex flex-col lg:flex-row gap-4">
                                <!-- Event Image -->
                                @if($booking->event->featured_image)
                                    <img src="{{ Storage::url($booking->event->featured_image) }}"
                                         alt="{{ $booking->event->title }}"
                                         class="w-full lg:w-48 h-32 object-cover rounded-lg">
                                @else
                                    <div class="w-full lg:w-48 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <x-icon.academic class="w-12 h-12 text-white opacity-50" />
                                    </div>
                                @endif

                                <!-- Event Info -->
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $booking->event->title }}</h3>

                                    <div class="space-y-1 text-sm text-gray-600 mb-4">
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

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('bookings.show', $booking->booking_number) }}"
                                           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                            Details ansehen
                                        </a>

                                        <a href="{{ route('bookings.invoice', $booking->booking_number) }}"
                                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                                            Rechnung
                                        </a>

                                        <!-- Review Button -->
                                        @php
                                            $userReview = $booking->event->reviews()->where('user_id', auth()->id())->first();
                                        @endphp

                                        @if(!$userReview)
                                            <a href="{{ route('events.show', $booking->event->slug) }}#reviews"
                                               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-sm font-medium">
                                                <x-icon.star class="w-4 h-4 inline-block mr-1" />
                                                Bewerten
                                            </a>
                                        @else
                                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium inline-flex items-center">
                                                <x-icon.check class="w-4 h-4 mr-1" />
                                                Bewertet
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Price & Date -->
                                <div class="lg:text-right">
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ number_format($booking->total, 2, ',', '.') }} €
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $booking->event->start_date->diffForHumans() }}
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
                    <x-icon.check class="w-24 h-24 text-gray-300 mx-auto mb-4" />
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Noch keine besuchten Veranstaltungen</h2>
                    <p class="text-gray-600 mb-6">
                        Ihre abgeschlossenen Fortbildungen erscheinen hier
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

