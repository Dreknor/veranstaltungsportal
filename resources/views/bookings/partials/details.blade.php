@php
    $event = $booking->event;
    $organization = $event->organization;
    $isExternalInvoicing = $organization?->hasExternalInvoicing() ?? false;
    $bankAccount = $organization?->bank_account ?? [];
    $organizerEmail = $event->getOrganizerEmail();
    $organizerPhone = $event->getOrganizerPhone();
    $isCancelled = $booking->status === 'cancelled';
    $isReady = $booking->isReadyForParticipation();
    $eventPast = $event->end_date?->isPast() ?? false;
    $needsAttendees = $booking->canBePersonalized() && !$booking->tickets_personalized;

    $statusBadge = match (true) {
        $isCancelled => ['Storniert', 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200'],
        $booking->status === 'completed' => ['Abgeschlossen', 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200'],
        $booking->status === 'pending_approval' => ['Wartet auf Freigabe', 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'],
        $isReady => ['Bestätigt', 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200'],
        !$booking->isPaymentComplete() && !$booking->isFree() => ['Zahlung offen', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200'],
        default => [$booking->statusLabel(), 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'],
    };

    $headline = match (true) {
        $isCancelled => 'Stornierte Buchung',
        $booking->status === 'pending_approval' => 'Ihre Anmeldung',
        $isReady => 'Ihre Buchung ist bestätigt',
        default => 'Ihre Buchung',
    };
@endphp

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 sm:pb-8">
    {{-- Meldungen --}}
    @foreach([
        'success' => 'bg-green-50 border-green-300 text-green-800',
        'info' => 'bg-blue-50 border-blue-300 text-blue-800',
        'warning' => 'bg-yellow-50 border-yellow-300 text-yellow-800',
        'error' => 'bg-red-50 border-red-300 text-red-800',
    ] as $key => $classes)
        @if(session($key))
            <div class="{{ $classes }} border px-4 py-3 rounded-lg mb-4 text-sm" role="{{ $key === 'error' ? 'alert' : 'status' }}">
                {{ session($key) }}
            </div>
        @endif
    @endforeach

    {{-- Kopf mit Status und Fortschritt --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6 mb-4">
        <div class="flex flex-wrap items-start justify-between gap-2 mb-5">
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $headline }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Buchungsnr. <span class="font-mono font-semibold select-all">{{ $booking->booking_number }}</span>
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusBadge[1] }}">{{ $statusBadge[0] }}</span>
        </div>
        <x-booking-progress :booking="$booking" />
    </div>

    {{-- Nächster Schritt: genau eine klare Handlungsaufforderung --}}
    @unless($isCancelled)
        <div class="rounded-xl border-2 p-4 sm:p-5 mb-4
            {{ $isReady && !$needsAttendees ? 'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800' : 'border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800' }}">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-2">Nächster Schritt</h2>

            @if($booking->status === 'pending_approval')
                <p class="text-gray-900 dark:text-gray-100 font-medium">Ihre Anmeldung wird vom Veranstalter geprüft.</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                    Sie müssen nichts weiter tun. Sobald Ihre Teilnahme bestätigt ist, erhalten Sie eine E-Mail
                    @if($event->requiresOnlineInfo()) mit den Zugangsdaten @elseif($booking->hasTicketDocument()) mit Ihren Tickets @endif.
                </p>

            @elseif(!$booking->isPaymentComplete() && !$booking->isFree() && !$isReady)
                @if($booking->canRetryOnlinePayment())
                    <p class="text-gray-900 dark:text-gray-100 font-medium">Bitte schließen Sie die Zahlung ab.</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                        Ihre Plätze sind reserviert. Nach der Zahlung erhalten Sie sofort
                        {{ $event->isOnline() ? 'die Zugangsdaten' : 'Ihre Tickets' }} per E-Mail.
                        Nicht abgeschlossene Zahlungen werden nach 48 Stunden automatisch storniert.
                    </p>
                    <div class="mt-4 flex flex-col sm:flex-row gap-2">
                        <form method="POST" action="{{ route('bookings.pay', $booking->booking_number) }}">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-[#0070ba] text-white font-semibold rounded-lg hover:bg-[#005ea6] transition">
                                Mit PayPal bezahlen ({{ number_format($booking->total, 2, ',', '.') }} €)
                            </button>
                        </form>
                        <form method="POST" action="{{ route('bookings.switch-to-invoice', $booking->booking_number) }}"
                              onsubmit="return confirm('Möchten Sie stattdessen per Rechnung (Überweisung) bezahlen?')">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-5 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Stattdessen per Rechnung zahlen
                            </button>
                        </form>
                    </div>
                @elseif($isExternalInvoicing)
                    <p class="text-gray-900 dark:text-gray-100 font-medium">Sie erhalten eine Rechnung vom Veranstalter.</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                        Sobald die Rechnung gestellt ist, bestätigen wir Ihre Buchung und senden Ihnen
                        {{ $event->isOnline() ? 'die Zugangsdaten' : ($booking->hasTicketDocument() ? 'Ihre Tickets' : 'alle Details') }} per E-Mail.
                    </p>
                @else
                    <p class="text-gray-900 dark:text-gray-100 font-medium">Bitte überweisen Sie {{ number_format($booking->total, 2, ',', '.') }} €.</p>
                    <dl class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm bg-white/70 dark:bg-gray-900/40 rounded-lg p-3">
                        <div><dt class="text-gray-500 dark:text-gray-400">Empfänger</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $bankAccount['account_holder'] ?? $organization?->name }}</dd></div>
                        @if(!empty($bankAccount['iban']))
                            <div><dt class="text-gray-500 dark:text-gray-400">IBAN</dt><dd class="font-mono font-medium text-gray-900 dark:text-gray-100 select-all break-all">{{ $bankAccount['iban'] }}</dd></div>
                        @endif
                        @if(!empty($bankAccount['bic']))
                            <div><dt class="text-gray-500 dark:text-gray-400">BIC</dt><dd class="font-mono text-gray-900 dark:text-gray-100">{{ $bankAccount['bic'] }}</dd></div>
                        @endif
                        <div><dt class="text-gray-500 dark:text-gray-400">Verwendungszweck</dt><dd class="font-mono font-medium text-gray-900 dark:text-gray-100 select-all">{{ $booking->invoice_number ?? $booking->booking_number }}</dd></div>
                    </dl>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-3">
                        Sobald die Zahlung verbucht ist, erhalten Sie automatisch
                        {{ $event->isOnline() ? 'die Zugangsdaten' : ($booking->hasTicketDocument() ? 'Ihre Tickets' : 'Ihre Bestätigung') }} per E-Mail.
                    </p>
                    @if($booking->invoice_number)
                        <a href="{{ route('bookings.invoice', $booking->booking_number) }}" class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-blue-700 dark:text-blue-400 hover:underline">
                            Rechnung herunterladen (PDF) →
                        </a>
                    @endif
                @endif

            @elseif($needsAttendees)
                <p class="text-gray-900 dark:text-gray-100 font-medium">Bitte tragen Sie die Teilnehmenden ein.</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                    Sie haben {{ $booking->items->count() }} Plätze gebucht.
                    @if($isReady) Danach senden wir Ihnen die personalisierten {{ $booking->hasTicketDocument() ? 'Tickets' : 'Bestätigungen' }} sofort per E-Mail.
                    @else Die {{ $booking->hasTicketDocument() ? 'Tickets' : 'Zugangsdaten' }} erhalten Sie nach Bestätigung bzw. Zahlungseingang. @endif
                </p>
                <a href="{{ route('bookings.personalize', $booking->booking_number) }}"
                   class="mt-4 inline-flex w-full sm:w-auto justify-center items-center px-5 py-3 bg-amber-500 text-white font-semibold rounded-lg hover:bg-amber-600 transition">
                    Teilnehmende eintragen
                </a>

            @elseif($isReady && !$eventPast)
                <p class="text-gray-900 dark:text-gray-100 font-medium">Alles erledigt – wir freuen uns auf Sie!</p>
                @if($booking->ticketsReleasedBeforePayment())
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Die Rechnung erhalten Sie separat vom Veranstalter – gegebenenfalls auch erst nach der Veranstaltung.</p>
                @endif
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                    @if($event->requiresOnlineInfo())
                        Ihre Zugangsdaten finden Sie unten. Zusätzlich erhalten Sie sie 24 Stunden und 3 Stunden vor Beginn per E-Mail.
                    @elseif($booking->hasTicketDocument())
                        Bringen Sie Ihr Ticket (ausgedruckt oder auf dem Smartphone) mit. Wir erinnern Sie 24 Stunden vor Beginn per E-Mail.
                    @else
                        Ihre Buchungsnummer genügt als Nachweis. Wir erinnern Sie 24 Stunden vor Beginn per E-Mail.
                    @endif
                </p>
                <div class="mt-4 flex flex-col sm:flex-row gap-2">
                    @if($booking->canAccessOnlineContent() && $event->online_url)
                        <a href="{{ $event->online_url }}" target="_blank" rel="noopener"
                           class="inline-flex justify-center items-center px-5 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                            Zur Online-Veranstaltung
                        </a>
                    @endif
                    @if($booking->hasTicketDocument() && $booking->canSendTickets())
                        <a href="{{ route('bookings.ticket', $booking->booking_number) }}"
                           class="inline-flex justify-center items-center px-5 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition">
                            Ticket herunterladen (PDF)
                        </a>
                    @endif
                    <a href="{{ route('bookings.ical', $booking->booking_number) }}"
                       class="inline-flex justify-center items-center px-5 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        In Kalender eintragen
                    </a>
                </div>

            @elseif($eventPast)
                <p class="text-gray-900 dark:text-gray-100 font-medium">Die Veranstaltung ist beendet. Vielen Dank für Ihre Teilnahme!</p>
                @if($isReady)
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Teilnahmezertifikate erhalten eingecheckte Teilnehmende unten bei den Tickets.</p>
                @endif
            @else
                <p class="text-gray-900 dark:text-gray-100 font-medium">Ihre Buchung wird bearbeitet.</p>
                <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Sie erhalten eine E-Mail, sobald sich etwas ändert.</p>
            @endif
        </div>
    @endunless

    {{-- Feedback nach der Veranstaltung --}}
    @if($eventPast && $isReady)
        <section id="feedback" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6 mb-4">
            @if($booking->review)
                <p class="font-medium text-gray-900 dark:text-gray-100">Danke für Ihr Feedback!</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ihre Bewertung: {{ str_repeat('★', $booking->review->rating) }}{{ str_repeat('☆', 5 - $booking->review->rating) }}</p>
            @else
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Wie hat Ihnen die Veranstaltung gefallen?</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Ihr Feedback hilft dem Veranstalter, zukünftige Angebote zu verbessern.</p>
                <form method="POST" action="{{ route('bookings.feedback', $booking->booking_number) }}" class="mt-4 space-y-3" x-data="{ rating: {{ (int) old('rating', 0) }} }">
                    @csrf
                    <fieldset>
                        <legend class="sr-only">Bewertung</legend>
                        <div class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only" x-model.number="rating" @checked(old('rating') == $i)>
                                    <span class="text-3xl leading-none transition" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-gray-300 dark:text-gray-600'" aria-hidden="true">★</span>
                                    <span class="sr-only">{{ $i }} von 5 Sternen</span>
                                </label>
                            @endfor
                        </div>
                        @error('rating')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </fieldset>
                    <label for="feedback-comment" class="sr-only">Kommentar</label>
                    <textarea id="feedback-comment" name="comment" rows="3" maxlength="1000" placeholder="Was war besonders hilfreich? Was können wir verbessern? (optional)"
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">{{ old('comment') }}</textarea>
                    <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">Feedback senden</button>
                </form>
            @endif
        </section>
    @endif

    {{-- Hinweis Konto für Gäste --}}
    @if(!auth()->check() && !$booking->user_id && session()->has('booking_access_' . $booking->id) && !$isCancelled)
        @php $existingUser = \App\Models\User::where('email', $booking->customer_email)->exists(); @endphp
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-4">
            <p class="flex-1 text-sm text-blue-900 dark:text-blue-100">
                @if($existingUser)
                    <strong>Mit Ihrem Konto verknüpfen:</strong> So finden Sie diese Buchung jederzeit unter „Meine Buchungen“.
                @else
                    <strong>Tipp:</strong> Mit einem kostenlosen Konto haben Sie alle Buchungen, Tickets und Zertifikate an einem Ort.
                @endif
            </p>
            <a href="{{ route('bookings.create-account', $booking->booking_number) }}"
               class="inline-flex justify-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition">
                {{ $existingUser ? 'Jetzt verknüpfen' : 'Konto erstellen' }}
            </a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            {{-- Veranstaltung --}}
            <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Veranstaltung</h2>
                <div class="flex gap-4">
                    @if($event->featured_image)
                        <img src="{{ Storage::url($event->featured_image) }}" alt="" class="hidden sm:block w-28 h-28 object-cover rounded-lg border border-gray-200 dark:border-gray-700 shrink-0">
                    @endif
                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $event->title }}</h3>
                        <ul class="mt-2 space-y-1.5 text-sm text-gray-700 dark:text-gray-300">
                            <li class="flex items-start gap-2">
                                <x-icon.calendar class="w-4 h-4 mt-0.5 shrink-0" />
                                <span>{{ $event->start_date->translatedFormat('D, d.m.Y · H:i') }} Uhr
                                    @if($event->end_date) – {{ $event->end_date->isSameDay($event->start_date) ? $event->end_date->format('H:i') : $event->end_date->format('d.m.Y H:i') }} Uhr @endif
                                </span>
                            </li>
                            @if($event->requiresVenue() && ($event->venue_name || $event->location))
                                <li class="flex items-start gap-2">
                                    <x-icon.location class="w-4 h-4 mt-0.5 shrink-0" />
                                    <span>
                                        {{ $event->venue_name }}@if($event->venue_address), {{ $event->venue_address }}@endif
                                        @if($event->venue_city), {{ trim($event->venue_postal_code . ' ' . $event->venue_city) }}@endif
                                    </span>
                                </li>
                            @endif
                            <li class="flex items-center gap-2">
                                <x-event-type-badge :event="$event" />
                            </li>
                        </ul>
                        <a href="{{ route('events.show', $event->slug) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm mt-3 inline-block">Veranstaltungsdetails →</a>
                    </div>
                </div>

                {{-- Online-Zugang (Online & Hybrid) --}}
                @if($event->requiresOnlineInfo() && !$isCancelled)
                    @if($booking->canAccessOnlineContent() && $event->online_url)
                        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <p class="text-sm font-semibold text-blue-900 dark:text-blue-100 mb-1">Online-Zugang</p>
                            <a href="{{ $event->online_url }}" target="_blank" rel="noopener" class="text-sm text-blue-700 dark:text-blue-300 underline break-all">{{ $event->online_url }}</a>
                            @if($event->online_access_code)
                                <p class="mt-2 text-sm text-blue-900 dark:text-blue-100">Zugangscode:
                                    <code class="bg-white dark:bg-gray-900 px-2 py-0.5 rounded border border-blue-300 dark:border-blue-700 font-mono select-all">{{ $event->online_access_code }}</code>
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300">
                            🔒 Die Online-Zugangsdaten werden hier und per E-Mail freigeschaltet, sobald Ihre Buchung
                            {{ $booking->status === 'pending_approval' ? 'bestätigt' : 'bestätigt und bezahlt' }} ist.
                        </div>
                    @endif
                @endif

                @if($event->ticket_notes && $isReady)
                    <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg text-sm text-gray-800 dark:text-gray-200">
                        <p class="font-semibold mb-1">Hinweise des Veranstalters</p>
                        {!! nl2br(e($event->ticket_notes)) !!}
                    </div>
                @endif
            </section>

            {{-- Tickets / Teilnehmende --}}
            <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        {{ $booking->items->count() > 1 ? 'Plätze & Teilnehmende' : 'Ihr Platz' }}
                    </h2>
                    @if($booking->canBePersonalized())
                        <a href="{{ route('bookings.personalize', $booking->booking_number) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $booking->tickets_personalized ? 'Teilnehmende ändern' : 'Teilnehmende eintragen' }}
                        </a>
                    @endif
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($booking->items as $item)
                        <li class="py-3 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $item->ticketType?->name ?? 'Ticket' }}</p>
                                <p class="text-sm {{ $item->attendee_name ? 'text-gray-700 dark:text-gray-300' : 'text-amber-700 dark:text-amber-400' }}">
                                    {{ $item->attendee_name ?? 'Teilnehmer:in noch nicht eingetragen' }}
                                    @if($item->attendee_email)<span class="text-gray-500 dark:text-gray-400 break-all"> · {{ $item->attendee_email }}</span>@endif
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $item->ticket_number }}</p>
                                @if($item->checked_in && $item->checked_in_at)
                                    <p class="text-xs text-green-700 dark:text-green-400 mt-1">✓ Eingecheckt am {{ $item->checked_in_at->format('d.m.Y H:i') }} Uhr</p>
                                @endif
                            </div>
                            <div class="text-right shrink-0">
                                @if(!$booking->isFree())
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($item->price, 2, ',', '.') }} €</p>
                                @endif
                                @if($item->checked_in && $eventPast)
                                    <a href="{{ route('bookings.certificate.individual', ['bookingNumber' => $booking->booking_number, 'itemId' => $item->id]) }}"
                                       class="inline-block text-xs text-purple-700 dark:text-purple-400 hover:underline mt-1">Zertifikat</a>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </section>

            {{-- Kundendaten --}}
            <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Ihre Angaben</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div><dt class="text-gray-500 dark:text-gray-400">Name</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->customer_name }}</dd></div>
                    <div><dt class="text-gray-500 dark:text-gray-400">E-Mail</dt><dd class="text-gray-900 dark:text-gray-100 break-all">{{ $booking->customer_email }}</dd></div>
                    @if($booking->customer_phone)
                        <div><dt class="text-gray-500 dark:text-gray-400">Telefon</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->customer_phone }}</dd></div>
                    @endif
                    @if($booking->customer_organization)
                        <div><dt class="text-gray-500 dark:text-gray-400">Einrichtung</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->customer_organization }}</dd></div>
                    @endif
                    @if($booking->billing_company)
                        <div><dt class="text-gray-500 dark:text-gray-400">Firma (Rechnung)</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->billing_company }}</dd></div>
                    @endif
                    @if($booking->billing_vat_id)
                        <div><dt class="text-gray-500 dark:text-gray-400">USt-IdNr.</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->billing_vat_id }}</dd></div>
                    @endif
                </dl>
            </section>
        </div>

        {{-- Seitenleiste --}}
        <aside class="space-y-4">
            <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Zusammenfassung</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Plätze</dt><dd class="font-medium text-gray-900 dark:text-gray-100">{{ $booking->items->count() }}</dd></div>
                    @if($booking->isFree())
                        <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Kosten</dt><dd class="font-medium text-green-700 dark:text-green-400">kostenfrei</dd></div>
                    @else
                        <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Zwischensumme</dt><dd class="text-gray-900 dark:text-gray-100">{{ number_format($booking->subtotal, 2, ',', '.') }} €</dd></div>
                        @if($booking->discount > 0)
                            <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Rabatt</dt><dd class="text-green-700 dark:text-green-400">−{{ number_format($booking->discount, 2, ',', '.') }} €</dd></div>
                        @endif
                        <div class="flex justify-between border-t border-gray-100 dark:border-gray-700 pt-2 text-base font-bold text-gray-900 dark:text-gray-100"><dt>Gesamt</dt><dd>{{ number_format($booking->total, 2, ',', '.') }} €</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Zahlart</dt><dd class="text-gray-900 dark:text-gray-100">{{ $booking->payment_method === 'paypal' ? 'PayPal' : 'Rechnung' }}</dd></div>
                        @if($booking->ticketsReleasedBeforePayment())
                            <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Rechnung</dt><dd class="text-gray-900 dark:text-gray-100">folgt separat</dd></div>
                        @elseif($booking->payment_status !== 'extern')
                            <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-400">Zahlung</dt><dd class="font-medium {{ $booking->isPaymentComplete() ? 'text-green-700 dark:text-green-400' : 'text-yellow-700 dark:text-yellow-400' }}">{{ $booking->paymentStatusLabel() }}</dd></div>
                        @endif
                    @endif
                </dl>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Gebucht am {{ $booking->created_at->format('d.m.Y H:i') }} Uhr</p>
            </section>

            <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Dokumente & Aktionen</h2>
                <div class="space-y-2">
                    @if($booking->hasTicketDocument() && $isReady && $booking->canSendTickets())
                        <a href="{{ route('bookings.ticket', $booking->booking_number) }}" class="block w-full px-4 py-2.5 text-center bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium">Ticket (PDF)</a>
                    @endif
                    @if(!$booking->isFree() && !$isCancelled && !$isExternalInvoicing && $booking->invoice_number)
                        <a href="{{ route('bookings.invoice', $booking->booking_number) }}" class="block w-full px-4 py-2.5 text-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-medium">Rechnung herunterladen (PDF)</a>
                    @elseif(!$booking->isFree() && $isExternalInvoicing)
                        <p class="text-xs text-gray-600 dark:text-gray-400">Die Rechnung erhalten Sie separat vom Veranstalter.</p>
                    @endif
                    @if($isReady && $eventPast)
                        <a href="{{ route('bookings.certificate', $booking->booking_number) }}" class="block w-full px-4 py-2.5 text-center bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">Teilnahmezertifikat</a>
                    @endif
                    @if(!$isCancelled && !$eventPast)
                        <a href="{{ route('bookings.ical', $booking->booking_number) }}" class="block w-full px-4 py-2.5 text-center bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-medium">Kalendereintrag (.ics)</a>
                    @endif
                    <button type="button" onclick="window.print()" class="hidden sm:block w-full px-4 py-2.5 text-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 text-sm">Seite drucken</button>

                    @if($booking->isActive() && $booking->status !== 'completed' && $event->start_date->isFuture())
                        <div class="pt-3 mt-2 border-t border-gray-100 dark:border-gray-700">
                            @if($event->canCancelBooking())
                                <form method="POST" action="{{ route('bookings.cancel', $booking->booking_number) }}"
                                      onsubmit="return confirm('Möchten Sie diese Buchung wirklich stornieren? Dies kann nicht rückgängig gemacht werden.')">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2.5 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition text-sm font-medium">Buchung stornieren</button>
                                </form>
                                @if($event->cancellation_days_before !== null)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">Kostenfrei möglich bis {{ $event->start_date->copy()->subDays($event->cancellation_days_before)->format('d.m.Y') }}</p>
                                @endif
                            @elseif($event->cancellation_allowed && $event->cancellation_days_before !== null)
                                <p class="text-xs text-gray-600 dark:text-gray-400">Die Stornierungsfrist ist abgelaufen (bis {{ $event->cancellation_days_before }} Tag(e) vor Beginn). Bitte wenden Sie sich an den Veranstalter.</p>
                            @else
                                <p class="text-xs text-gray-600 dark:text-gray-400">Eine Online-Stornierung ist für diese Veranstaltung nicht möglich. Bitte wenden Sie sich an den Veranstalter.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </section>

            <section class="bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Fragen zur Buchung?</h3>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $event->getOrganizerName() }}</p>
                @if($organizerEmail)
                    <a href="mailto:{{ $organizerEmail }}?subject={{ rawurlencode('Buchung ' . $booking->booking_number) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $organizerEmail }}</a>
                @endif
                @if($organizerPhone)
                    <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $organizerPhone) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ $organizerPhone }}</a></p>
                @endif
            </section>
        </aside>
    </div>
</div>
