<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6">
            {{ session('info') }}
        </div>
    @endif

    <!-- Personalization Alert -->
    @if($booking->needsPersonalization())
        <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-6 mb-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-yellow-600 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-semibold text-yellow-900 mb-2">Ticket-Personalisierung erforderlich</h3>
                    <p class="text-yellow-800 text-sm mb-4">
                        Sie haben mehrere Tickets gebucht. Bitte personalisieren Sie Ihre Tickets, damit diese per E-Mail versendet werden können.
                    </p>
                    <a href="{{ route('bookings.personalize', $booking->booking_number) }}"
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition">
                        <x-icon.user class="w-4 h-4 mr-2" />
                        Jetzt personalisieren
                    </a>
                </div>
            </div>
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
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Bestätigt</span>
                @elseif($booking->status === 'pending')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Ausstehend</span>
                @elseif($booking->status === 'cancelled')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Storniert</span>
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
                        <img src="{{ Storage::url($booking->event->featured_image) }}" alt="{{ $booking->event->title }}" class="w-24 h-24 object-cover rounded-lg">
                    @endif
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 mb-2">{{ $booking->event->title }}</h3>
                        <div class="space-y-1 text-sm text-gray-600">
                            @if($booking->event->start_date)
                                <div class="flex items-center">
                                    <x-icon.calendar class="w-4 h-4 mr-2" />
                                    {{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr
                                </div>
                            @endif

                            @if($booking->event->isOnline())
                                <div class="flex items-center text-blue-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                    Online-Veranstaltung
                                </div>
                                @if($booking->payment_status === 'paid' && $booking->event->online_url)
                                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                            <div class="flex-1">
                                                <div class="text-xs font-semibold text-blue-900 mb-1">Online-Zugang:</div>
                                                <a href="{{ $booking->event->online_url }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 break-all font-medium underline">
                                                    {{ $booking->event->online_url }}
                                                </a>
                                                @if($booking->event->online_access_code)
                                                    <div class="mt-2">
                                                        <div class="text-xs font-semibold text-blue-900 mb-1">Zugangscode:</div>
                                                        <code class="text-sm bg-white px-2 py-1 rounded border border-blue-300 font-mono">{{ $booking->event->online_access_code }}</code>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif($booking->payment_status !== 'paid')
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                                        ℹ️ Die Zugangsdaten werden nach Zahlungseingang freigeschaltet.
                                    </div>
                                @endif
                            @elseif($booking->event->isHybrid())
                                <div class="flex items-center text-purple-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Hybrid-Veranstaltung
                                </div>
                                @if($booking->event->venue_name || $booking->event->location)
                                    <div class="flex items-center ml-6 text-gray-600">
                                        <x-icon.location class="w-4 h-4 mr-2" />
                                        Präsenz:
                                        @if($booking->event->venue_name)
                                            {{ $booking->event->venue_name }}@if($booking->event->venue_city), {{ $booking->event->venue_city }}@endif
                                        @elseif($booking->event->location)
                                            {{ $booking->event->location }}
                                        @endif
                                    </div>
                                @endif
                                @if($booking->payment_status === 'paid' && $booking->event->online_url)
                                    <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-5 h-5 text-purple-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                            <div class="flex-1">
                                                <div class="text-xs font-semibold text-purple-900 mb-1">Online-Zugang:</div>
                                                <a href="{{ $booking->event->online_url }}" target="_blank" class="text-sm text-purple-600 hover:text-purple-800 break-all font-medium underline">
                                                    {{ $booking->event->online_url }}
                                                </a>
                                                @if($booking->event->online_access_code)
                                                    <div class="mt-2">
                                                        <div class="text-xs font-semibold text-purple-900 mb-1">Zugangscode:</div>
                                                        <code class="text-sm bg-white px-2 py-1 rounded border border-purple-300 font-mono">{{ $booking->event->online_access_code }}</code>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @elseif($booking->payment_status !== 'paid')
                                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-800">
                                        ℹ️ Die Online-Zugangsdaten werden nach Zahlungseingang freigeschaltet.
                                    </div>
                                @endif
                            @elseif($booking->event->venue_name || $booking->event->location)
                                <div class="flex items-center">
                                    <x-icon.location class="w-4 h-4 mr-2" />
                                    @if($booking->event->venue_name)
                                        {{ $booking->event->venue_name }}@if($booking->event->venue_city), {{ $booking->event->venue_city }}@endif
                                    @elseif($booking->event->location)
                                        {{ $booking->event->location }}
                                    @endif
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('events.show', $booking->event->slug) }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">Event-Details ansehen →</a>
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
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item->ticketType->name }}</h3>
                                    <p class="text-sm text-gray-600">Ticket-Nr: {{ $item->ticket_number }}</p>
                                    @if($item->attendee_name)
                                        <p class="text-sm text-blue-600 mt-1">
                                            <span class="font-medium">Teilnehmer:</span> {{ $item->attendee_name }}
                                            @if($item->attendee_email)
                                                <span class="text-gray-500">({{ $item->attendee_email }})</span>
                                            @endif
                                        </p>
                                    @endif
                                    @if($item->checked_in && $item->checked_in_at)
                                        <p class="text-sm text-green-600 mt-1">
                                            ✓ Eingecheckt am {{ $item->checked_in_at->format('d.m.Y H:i') }} Uhr
                                        </p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-gray-900">{{ number_format($item->price, 2, ',', '.') }} €</div>

                                    <!-- Zertifikat-Download für eingecheckte Teilnehmer nach Event-Ende -->
                                    @if($item->checked_in && $booking->event->end_date->isPast())
                                        <a href="{{ route('bookings.certificate.individual', ['bookingNumber' => $booking->booking_number, 'itemId' => $item->id]) }}"
                                           class="inline-flex items-center text-xs text-purple-600 hover:text-purple-800 mt-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z"/>
                                                <path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                            </svg>
                                            Zertifikat
                                        </a>
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
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Bezahlt</span>
                        @elseif($booking->payment_status === 'pending')
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Ausstehend</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mt-2">Gebucht am {{ $booking->created_at->format('d.m.Y H:i') }} Uhr</div>
                </div>
            </div>

            <!-- Aktionen -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Aktionen</h2>
                <div class="space-y-3">
                    @if($booking->status === 'confirmed' && $booking->payment_status === 'paid')
                        <a href="{{ route('bookings.ticket', $booking->booking_number) }}" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition block text-center">🎫 Ticket herunterladen (PDF)</a>
                        <a href="{{ route('bookings.invoice', $booking->booking_number) }}" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition block text-center">📄 Rechnung herunterladen (PDF)</a>
                        @if($booking->event->end_date->isPast())
                            <a href="{{ route('bookings.certificate', $booking->booking_number) }}" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition block text-center">🏆 Teilnahmezertifikat herunterladen</a>
                        @endif
                    @else
                        <div class="w-full px-4 py-3 bg-gray-100 text-gray-600 rounded-lg text-center text-sm">Tickets und Dokumente sind verfügbar, sobald die Buchung bestätigt und bezahlt wurde.</div>
                    @endif

                    <button onclick="window.print()" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">🖨️ Diese Seite drucken</button>

                    @if($booking->status !== 'cancelled' && $booking->event->start_date->isFuture())
                        <form method="POST" action="{{ route('bookings.cancel', $booking->booking_number) }}" onsubmit="return confirm('Möchten Sie diese Buchung wirklich stornieren?')">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Buchung stornieren</button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Hilfe -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Fragen?</h3>
                <p class="text-sm text-blue-800">Bei Fragen zu Ihrer Buchung kontaktieren Sie bitte den Veranstalter.</p>
                @if($booking->event->organizer_email)
                    <a href="mailto:{{ $booking->event->organizer_email }}" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">{{ $booking->event->organizer_email }}</a>
                @endif
            </div>
        </div>
    </div>
</div>

