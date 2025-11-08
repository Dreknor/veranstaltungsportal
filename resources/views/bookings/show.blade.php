<x-layouts.public :title="'Buchung ' . $booking->booking_number">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Buchungsbestätigung</h1>
                        <p class="text-gray-600 mt-1">Buchungsnummer: <span class="font-mono font-semibold">{{ $booking->booking_number }}</span></p>
                    </div>
                    <div>
                        @if($booking->status === 'confirmed')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Bestätigt
                            </span>
                        @elseif($booking->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                Ausstehend
                            </span>
                        @elseif($booking->status === 'cancelled')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Storniert
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Hauptbereich -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Event Details -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Veranstaltung</h2>
                        <div class="flex gap-4">
                            @if($booking->event->featured_image)
                                <img src="{{ Storage::url($booking->event->featured_image) }}" alt="{{ $booking->event->title }}"
                                     class="w-24 h-24 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-2">{{ $booking->event->title }}</h3>
                                <div class="space-y-1 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <x-icon.calendar class="w-4 h-4 mr-2" />
                                        {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr
                                    </div>
                                    <div class="flex items-center">
                                        <x-icon.location class="w-4 h-4 mr-2" />
                                        {{ $booking->event->venue_name }}, {{ $booking->event->venue_city }}
                                    </div>
                                </div>
                                <a href="{{ route('events.show', $booking->event->slug) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                    Event-Details ansehen →
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Ihre Tickets</h2>
                        <div class="space-y-4">
                            @foreach($booking->items as $item)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $item->ticketType->name }}</h3>
                                            <p class="text-sm text-gray-600">Ticket-Nr: {{ $item->ticket_number }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-semibold text-gray-900">{{ number_format($item->price, 2, ',', '.') }} €</div>
                                            @if($item->checked_in)
                                                <span class="text-xs text-green-600">✓ Eingecheckt</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($item->ticketType->description)
                                        <p class="text-sm text-gray-600 mt-2">{{ $item->ticketType->description }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Kundendaten -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Kundendaten</h2>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->customer_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">E-Mail</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $booking->customer_email }}</dd>
                            </div>
                            @if($booking->customer_phone)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $booking->customer_phone }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Zusammenfassung -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Zusammenfassung</h2>
                        <dl class="space-y-3">
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Anzahl Tickets:</dt>
                                <dd class="font-medium">{{ $booking->items->count() }}</dd>
                            </div>
                            <div class="flex justify-between text-sm">
                                <dt class="text-gray-600">Zwischensumme:</dt>
                                <dd class="font-medium">{{ number_format($booking->subtotal, 2, ',', '.') }} €</dd>
                            </div>
                            @if($booking->discount > 0)
                                <div class="flex justify-between text-sm">
                                    <dt class="text-gray-600">Rabatt:</dt>
                                    <dd class="font-medium text-green-600">-{{ number_format($booking->discount, 2, ',', '.') }} €</dd>
                                </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold border-t pt-3">
                                <dt>Gesamt:</dt>
                                <dd>{{ number_format($booking->total, 2, ',', '.') }} €</dd>
                            </div>
                        </dl>

                        <div class="mt-4 pt-4 border-t">
                            <div class="text-sm">
                                <span class="text-gray-600">Zahlungsstatus:</span>
                                @if($booking->payment_status === 'paid')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Bezahlt
                                    </span>
                                @elseif($booking->payment_status === 'pending')
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Ausstehend
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                Gebucht am {{ $booking->created_at->format('d.m.Y H:i') }} Uhr
                            </div>
                        </div>
                    </div>

                    <!-- Aktionen -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Aktionen</h2>
                        <div class="space-y-3">
                            <a href="{{ route('bookings.ticket', $booking->booking_number) }}"
                               class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition block text-center">
                                🎫 Ticket herunterladen (PDF)
                            </a>

                            <a href="{{ route('bookings.invoice', $booking->booking_number) }}"
                               class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition block text-center">
                                📄 Rechnung herunterladen (PDF)
                            </a>

                            @if($booking->status === 'confirmed' && $booking->event->end_date->isPast())
                                <a href="{{ route('bookings.certificate', $booking->booking_number) }}"
                                   class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition block text-center">
                                    🏆 Teilnahmezertifikat herunterladen
                                </a>
                            @endif

                            <button onclick="window.print()"
                                    class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                🖨️ Diese Seite drucken
                            </button>

                            @if($booking->status !== 'cancelled' && $booking->event->start_date->isFuture())
                                <form method="POST" action="{{ route('bookings.cancel', $booking->booking_number) }}"
                                      onsubmit="return confirm('Möchten Sie diese Buchung wirklich stornieren?')">
                                    @csrf
                                    <button type="submit"
                                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                        Buchung stornieren
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Hilfe -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 mb-2">Fragen?</h3>
                        <p class="text-sm text-blue-800">
                            Bei Fragen zu Ihrer Buchung kontaktieren Sie bitte den Veranstalter.
                        </p>
                        @if($booking->event->organizer_email)
                            <a href="mailto:{{ $booking->event->organizer_email }}"
                               class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                {{ $booking->event->organizer_email }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>

