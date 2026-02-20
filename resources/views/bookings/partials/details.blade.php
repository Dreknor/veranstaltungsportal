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

    <!-- Account Creation/Linking Prompt for Guest Bookings -->
    @if(!auth()->check() && !$booking->user_id && session()->has('booking_access_' . $booking->id))
        @php
            $existingUser = \App\Models\User::where('email', $booking->customer_email)->first();
        @endphp

        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-lg p-6 mb-6 shadow-sm">
            <div class="flex items-start">
                <svg class="w-8 h-8 text-blue-600 mr-4 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-blue-900 mb-2 text-lg">
                        @if($existingUser)
                            Buchung mit Ihrem Konto verknüpfen
                        @else
                            Erstellen Sie ein Benutzerkonto
                        @endif
                    </h3>

                    @if($existingUser)
                        <p class="text-blue-800 mb-4">
                            Wir haben festgestellt, dass für Ihre E-Mail-Adresse bereits ein Konto existiert.
                            Verknüpfen Sie diese Buchung mit Ihrem Konto, um:
                        </p>
                    @else
                        <p class="text-blue-800 mb-4">
                            Erstellen Sie ein kostenloses Benutzerkonto, um diese und zukünftige Buchungen besser zu verwalten:
                        </p>
                    @endif

                    <ul class="text-sm text-blue-700 space-y-2 mb-4 ml-4">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Alle Buchungen an einem Ort verwalten
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Schnellerer Buchungsprozess bei zukünftigen Veranstaltungen
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Zugriff auf persönliche Empfehlungen
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Teilnahmezertifikate automatisch im Profil
                        </li>
                    </ul>

                    <a href="{{ route('bookings.create-account', $booking->booking_number) }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        @if($existingUser)
                            Jetzt verknüpfen
                        @else
                            Konto erstellen
                        @endif
                    </a>
                </div>
            </div>
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
                        <div class="flex-shrink-0">
                            <img src="{{ Storage::url($booking->event->featured_image) }}" alt="{{ $booking->event->title }}" class="w-32 h-32 object-contain rounded-lg border border-gray-200">
                        </div>
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
                    <div class="text-sm mb-3">
                        <span class="text-gray-600">Zahlungsmethode:</span>
                        @if($booking->payment_method === 'paypal')
                            <span class="ml-2 inline-flex items-center">
                                <svg class="w-16 h-4 mr-1" viewBox="0 0 124 33" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M46.211 6.749h-6.839a.95.95 0 0 0-.939.802l-2.766 17.537a.57.57 0 0 0 .564.658h3.265a.95.95 0 0 0 .939-.803l.746-4.73a.95.95 0 0 1 .938-.803h2.165c4.505 0 7.105-2.18 7.784-6.5.306-1.89.013-3.375-.872-4.415-.972-1.142-2.696-1.746-4.985-1.746zM47 13.154c-.374 2.454-2.249 2.454-4.062 2.454h-1.032l.724-4.583a.57.57 0 0 1 .563-.481h.473c1.235 0 2.4 0 3.002.704.359.42.469 1.044.332 1.906zM66.654 13.075h-3.275a.57.57 0 0 0-.563.481l-.145.916-.229-.332c-.709-1.029-2.29-1.373-3.868-1.373-3.619 0-6.71 2.741-7.312 6.586-.313 1.918.132 3.752 1.22 5.031.998 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .562.66h2.95a.95.95 0 0 0 .939-.803l1.77-11.209a.568.568 0 0 0-.561-.658zm-4.565 6.374c-.316 1.871-1.801 3.127-3.695 3.127-.951 0-1.711-.305-2.199-.883-.484-.574-.668-1.391-.514-2.301.295-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.499.589.697 1.411.554 2.317zM84.096 13.075h-3.291a.954.954 0 0 0-.787.417l-4.539 6.686-1.924-6.425a.953.953 0 0 0-.912-.678h-3.234a.57.57 0 0 0-.541.754l3.625 10.638-3.408 4.811a.57.57 0 0 0 .465.9h3.287a.949.949 0 0 0 .781-.408l10.946-15.8a.57.57 0 0 0-.468-.895z" fill="#253B80"/>
                                    <path d="M94.992 6.749h-6.84a.95.95 0 0 0-.938.802l-2.766 17.537a.569.569 0 0 0 .562.658h3.51a.665.665 0 0 0 .656-.562l.785-4.971a.95.95 0 0 1 .938-.803h2.164c4.506 0 7.105-2.18 7.785-6.5.307-1.89.012-3.375-.873-4.415-.971-1.142-2.694-1.746-4.983-1.746zm.789 6.405c-.373 2.454-2.248 2.454-4.062 2.454h-1.031l.725-4.583a.568.568 0 0 1 .562-.481h.473c1.234 0 2.4 0 3.002.704.359.42.468 1.044.331 1.906zM115.434 13.075h-3.273a.567.567 0 0 0-.562.481l-.145.916-.23-.332c-.709-1.029-2.289-1.373-3.867-1.373-3.619 0-6.709 2.741-7.311 6.586-.312 1.918.131 3.752 1.219 5.031 1 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .564.66h2.949a.95.95 0 0 0 .938-.803l1.771-11.209a.571.571 0 0 0-.565-.658zm-4.565 6.374c-.314 1.871-1.801 3.127-3.695 3.127-.949 0-1.711-.305-2.199-.883-.484-.574-.666-1.391-.514-2.301.297-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.501.589.699 1.411.554 2.317zM119.295 7.23l-2.807 17.858a.569.569 0 0 0 .562.658h2.822c.469 0 .867-.34.939-.803l2.768-17.536a.57.57 0 0 0-.562-.659h-3.16a.571.571 0 0 0-.562.482z" fill="#179BD7"/>
                                    <path d="M7.266 29.154l.523-3.322-1.165-.027H1.061L4.927 1.292a.316.316 0 0 1 .314-.268h9.38c3.114 0 5.263.648 6.385 1.927.526.6.861 1.227 1.023 1.917.17.724.173 1.589.007 2.644l-.012.077v.676l.526.298a3.69 3.69 0 0 1 1.065.812c.45.513.741 1.165.864 1.938.127.795.085 1.741-.123 2.812-.24 1.232-.628 2.305-1.152 3.183a6.547 6.547 0 0 1-1.825 2c-.696.494-1.523.869-2.458 1.109-.906.236-1.939.355-3.072.355h-.73c-.522 0-1.029.188-1.427.525a2.21 2.21 0 0 0-.744 1.328l-.055.299-.924 5.855-.042.215c-.011.068-.03.102-.058.125a.155.155 0 0 1-.096.035H7.266z" fill="#253B80"/>
                                    <path d="M23.048 7.667c-.028.179-.06.362-.096.55-1.237 6.351-5.469 8.545-10.874 8.545H9.326c-.661 0-1.218.48-1.321 1.132L6.596 26.83l-.399 2.533a.704.704 0 0 0 .695.814h4.881c.578 0 1.069-.42 1.16-.99l.048-.248.919-5.832.059-.32c.09-.572.582-.992 1.16-.992h.73c4.729 0 8.431-1.92 9.513-7.476.452-2.321.218-4.259-.978-5.622a4.667 4.667 0 0 0-1.336-1.03z" fill="#179BD7"/>
                                    <path d="M21.754 7.151a9.757 9.757 0 0 0-1.203-.267 15.284 15.284 0 0 0-2.426-.177h-7.352a1.172 1.172 0 0 0-1.159.992L8.05 17.605l-.045.289a1.336 1.336 0 0 1 1.321-1.132h2.752c5.405 0 9.637-2.195 10.874-8.545.037-.188.068-.371.096-.55a6.594 6.594 0 0 0-1.017-.429 9.045 9.045 0 0 0-.277-.087z" fill="#222D65"/>
                                    <path d="M9.614 7.699a1.169 1.169 0 0 1 1.159-.991h7.352c.871 0 1.684.057 2.426.177a9.757 9.757 0 0 1 1.481.353c.365.121.704.264 1.017.429.368-2.347-.003-3.945-1.272-5.392C20.378.682 17.853 0 14.622 0h-9.38c-.66 0-1.223.48-1.325 1.133L.01 25.898a.806.806 0 0 0 .795.932h5.791l1.454-9.225 1.564-9.906z" fill="#253B80"/>
                                </svg>
                            </span>
                        @elseif($booking->payment_method === 'invoice')
                            <span class="ml-2 inline-flex items-center text-gray-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Rechnung
                            </span>
                        @else
                            <span class="ml-2 text-gray-700">{{ $booking->payment_method ?? 'Nicht angegeben' }}</span>
                        @endif
                    </div>
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
                        @if($booking->event->requires_ticket)
                            <a href="{{ route('bookings.ticket', $booking->booking_number) }}" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition block text-center">🎫 Ticket herunterladen (PDF)</a>
                        @else
                            <div class="w-full px-4 py-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg text-center text-sm">
                                ℹ️ Für diese Veranstaltung ist kein separates Ticket erforderlich. Ihre Buchungsnummer dient als Zugangsnachweis.
                            </div>
                        @endif
                        <a href="{{ route('bookings.invoice', $booking->booking_number) }}" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition block text-center">📄 Rechnung herunterladen (PDF)</a>
                        @if($booking->event->end_date->isPast())
                            <a href="{{ route('bookings.certificate', $booking->booking_number) }}" class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition block text-center">🏆 Teilnahmezertifikat herunterladen</a>
                        @endif
                    @else
                        <div class="w-full px-4 py-3 bg-gray-100 text-gray-600 rounded-lg text-center text-sm">
                            @if($booking->event->requires_ticket)
                                Tickets und Dokumente sind verfügbar, sobald die Buchung bestätigt und bezahlt wurde.
                            @else
                                Dokumente sind verfügbar, sobald die Buchung bestätigt und bezahlt wurde.
                            @endif
                        </div>
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

