<x-layouts.app title="Buchungsdetails">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('organizer.bookings.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    ← Zurück zur Übersicht
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Buchungsdetails</h1>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Buchungsinformationen -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $booking->booking_number }}</h2>
                        <p class="text-gray-600">Gebucht am {{ $booking->created_at->format('d.m.Y H:i') }} Uhr</p>
                    </div>
                    <div class="text-right">
                        @if($booking->status === 'pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Ausstehend</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Bestätigt</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Storniert</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Event -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Event</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->event->title }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr</p>
                        <p class="text-sm text-gray-600">{{ $booking->event->venue_name }}, {{ $booking->event->venue_city }}</p>
                    </div>

                    <!-- Kunde -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Kunde</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->customer_name }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->customer_email }}</p>
                        @if($booking->customer_phone)
                            <p class="text-sm text-gray-600">{{ $booking->customer_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tickets -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Tickets</h2>

                @if($booking->needsPersonalization())
                    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">
                                    Personalisierung erforderlich - Der Kunde muss die Tickets noch personalisieren, bevor sie versendet werden können.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($booking->tickets_personalized)
                    <div class="bg-green-50 border border-green-300 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-green-800">
                                    Tickets personalisiert am {{ $booking->tickets_personalized_at->format('d.m.Y H:i') }} Uhr
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    @foreach($booking->items as $item)
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item->ticketType->name }}</h3>
                                    <p class="text-sm text-gray-600">Ticket-Nr: {{ $item->ticket_number }}</p>
                                    @if($item->attendee_name)
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <p class="text-sm text-blue-600">
                                                <span class="font-medium">Teilnehmer:</span> {{ $item->attendee_name }}
                                            </p>
                                            @if($item->attendee_email)
                                                <p class="text-xs text-gray-500">{{ $item->attendee_email }}</p>
                                            @endif
                                        </div>
                                    @endif
                                    @if($item->checked_in)
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <p class="text-sm text-green-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Eingecheckt am {{ $item->checked_in_at->format('d.m.Y H:i') }} Uhr
                                            </p>
                                            @if($booking->event->end_date->isPast())
                                                <p class="text-xs text-purple-600 mt-1">
                                                    ✓ Zertifikat verfügbar
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">{{ number_format($item->price, 2, ',', '.') }} €</p>
                                    <p class="text-sm text-gray-600">Anzahl: {{ $item->quantity }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kosten -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Kosten</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Zwischensumme</span>
                        <span class="font-medium">{{ number_format($booking->subtotal, 2, ',', '.') }} €</span>
                    </div>
                    @if($booking->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Rabatt @if($booking->discountCode) ({{ $booking->discountCode->code }}) @endif</span>
                            <span>-{{ number_format($booking->discount, 2, ',', '.') }} €</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Gesamt</span>
                        <span>{{ number_format($booking->total, 2, ',', '.') }} €</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Zahlungsstatus</span>
                        @if($booking->payment_status === 'paid')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Bezahlt</span>
                        @elseif($booking->payment_status === 'pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Ausstehend</span>
                        @elseif($booking->payment_status === 'refunded')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Erstattet</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Fehlgeschlagen</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Aktionen -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Aktionen</h2>
                <div class="flex flex-wrap gap-4">
                    <!-- Status ändern -->
                    @if($booking->status !== 'cancelled')
                        <form action="{{ route('organizer.bookings.update-status', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 shadow-sm">
                                <option value="">Status ändern...</option>
                                <option value="pending" {{ $booking->status === 'pending' ? 'disabled' : '' }}>Ausstehend</option>
                                <option value="confirmed" {{ $booking->status === 'confirmed' ? 'disabled' : '' }}>Bestätigen</option>
                                <option value="completed" {{ $booking->status === 'completed' ? 'disabled' : '' }}>Abschließen</option>
                                <option value="cancelled" {{ $booking->status === 'cancelled' ? 'disabled' : '' }}>Stornieren</option>
                            </select>
                        </form>

                        <!-- Zahlungsstatus ändern -->
                        <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <select name="payment_status" onchange="this.form.submit()" class="rounded-lg border-gray-300 shadow-sm">
                                <option value="">Zahlungsstatus ändern...</option>
                                <option value="pending" {{ $booking->payment_status === 'pending' ? 'disabled' : '' }}>Ausstehend</option>
                                <option value="paid" {{ $booking->payment_status === 'paid' ? 'disabled' : '' }}>Bezahlt</option>
                                <option value="refunded" {{ $booking->payment_status === 'refunded' ? 'disabled' : '' }}>Erstattet</option>
                                <option value="failed" {{ $booking->payment_status === 'failed' ? 'disabled' : '' }}>Fehlgeschlagen</option>
                            </select>
                        </form>
                    @endif

                    <!-- Buchung ansehen (Kundenansicht) -->
                    <a href="{{ route('bookings.show', $booking->booking_number) }}" target="_blank"
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        Kundenansicht öffnen
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

