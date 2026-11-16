@php
    use App\Support\Ui;

    $event = $booking->event;
    $organization = $event->organization;
    $isExternalInvoicing = $organization?->hasExternalInvoicing() ?? false;
    $bankAccount = $organization?->bank_account ?? [];
    $organizerEmail = $event->getOrganizerEmail();
    $organizerPhone = $event->getOrganizerPhone();
    $isCancelled = $booking->status === 'cancelled';
    $isReady = $booking->isReadyForParticipation();
    $eventPast = $event->end_date?->isPast() ?? false;
    // Veranstalter-Team und Administration sehen die Kundenansicht nur lesend.
    $readOnly = auth()->check()
        && auth()->id() !== $booking->user_id
        && !session()->has('booking_access_' . $booking->id);
    $needsAttendees = !$readOnly && $booking->canBePersonalized() && !$booking->tickets_personalized;
    $isFree = $booking->isFree();
    $canCancelOnline = !$readOnly && $booking->isActive() && $booking->status !== 'completed' && $event->start_date->isFuture();

    // [Text, Ton für <x-ui.status>]
    $statusBadge = match (true) {
        $isCancelled => ['Storniert', 'danger'],
        $booking->status === 'completed' => ['Abgeschlossen', 'success'],
        $booking->status === 'pending_approval' => ['Wartet auf Freigabe', 'warning'],
        $isReady => ['Bestätigt', 'success'],
        !$booking->isPaymentComplete() && !$isFree => ['Zahlung offen', 'warning'],
        default => [$booking->statusLabel(), Ui::bookingTone($booking->status)],
    };

    $headline = match (true) {
        $isCancelled => 'Stornierte Buchung',
        $booking->status === 'pending_approval' => 'Ihre Anmeldung',
        $isReady => 'Ihre Buchung ist bestätigt',
        default => 'Ihre Buchung',
    };
@endphp

{{-- Kopf mit Buchungsnummer, Status und Fortschritt --}}
<x-ui.page-header :title="$headline" class="mb-6">
    <x-slot:meta>
        <span class="flex flex-wrap items-center gap-x-4 gap-y-1">
            <span class="tabular-nums">Buchungsnr. <span
                    class="font-semibold text-ink select-all">{{ $booking->booking_number }}</span></span>
            <x-ui.status :tone="$statusBadge[1]">{{ $statusBadge[0] }}</x-ui.status>
        </span>
    </x-slot:meta>
</x-ui.page-header>

<x-booking-progress :booking="$booking" class="mb-8" />

{{-- Nächster Schritt: genau eine klare Handlungsaufforderung --}}
@unless ($isCancelled)
    <x-ui.box as="section" tone="zone" class="mb-8" aria-labelledby="naechster-schritt">
        <h2 id="naechster-schritt" class="m-0 mb-2 text-small font-medium text-muted">Nächster Schritt</h2>

        @if ($booking->status === 'pending_approval')
            <p class="m-0 text-h3">Ihre Anmeldung wird vom Veranstalter geprüft.</p>
            <p class="mt-1 mb-0 text-base text-ink-soft">
                Sie müssen nichts weiter tun. Sobald Ihre Teilnahme bestätigt ist, erhalten Sie eine E-Mail
                @if ($event->requiresOnlineInfo())
                    mit den Zugangsdaten
                @elseif($booking->hasTicketDocument())
                    mit Ihren Tickets
                @endif.
            </p>
        @elseif(!$booking->isPaymentComplete() && !$isFree && !$isReady)
            @if ($booking->canRetryOnlinePayment())
                <p class="m-0 text-h3">Bitte schließen Sie die Zahlung ab.</p>
                <p class="mt-1 mb-0 text-base text-ink-soft">
                    Ihre Plätze sind reserviert. Nach der Zahlung erhalten Sie sofort
                    {{ $event->isOnline() ? 'die Zugangsdaten' : 'Ihre Tickets' }} per E-Mail.
                    Nicht abgeschlossene Zahlungen werden nach 48 Stunden automatisch storniert.
                </p>
                @unless ($readOnly)
                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                    <form method="POST" action="{{ route('bookings.pay', $booking->booking_number) }}" class="m-0">
                        @csrf
                        <x-ui.button type="submit" class="w-full sm:w-auto">
                            Mit PayPal bezahlen ({{ Ui::money($booking->total) }})
                        </x-ui.button>
                    </form>
                    <form method="POST" action="{{ route('bookings.switch-to-invoice', $booking->booking_number) }}"
                        class="m-0"
                        onsubmit="return confirm('Möchten Sie stattdessen per Rechnung (Überweisung) bezahlen?')">
                        @csrf
                        <x-ui.button type="submit" variant="secondary" class="w-full sm:w-auto">
                            Stattdessen per Rechnung zahlen
                        </x-ui.button>
                    </form>
                </div>
                @endunless
            @elseif($isExternalInvoicing)
                <p class="m-0 text-h3">Sie erhalten eine Rechnung vom Veranstalter.</p>
                <p class="mt-1 mb-0 text-base text-ink-soft">
                    Sobald die Rechnung gestellt ist, bestätigen wir Ihre Buchung und senden Ihnen
                    {{ $event->isOnline() ? 'die Zugangsdaten' : ($booking->hasTicketDocument() ? 'Ihre Tickets' : 'alle Details') }}
                    per E-Mail.
                </p>
            @else
                <p class="m-0 text-h3 tabular-nums">Bitte überweisen Sie {{ Ui::money($booking->total) }}.</p>
                <x-ui.dl layout="stack" class="mt-4">
                    <x-ui.dl-item label="Empfänger">{{ $bankAccount['account_holder'] ?? $organization?->name }}</x-ui.dl-item>
                    @if (!empty($bankAccount['iban']))
                        <x-ui.dl-item label="IBAN" num><span
                                class="break-all select-all">{{ $bankAccount['iban'] }}</span></x-ui.dl-item>
                    @endif
                    @if (!empty($bankAccount['bic']))
                        <x-ui.dl-item label="BIC" num>{{ $bankAccount['bic'] }}</x-ui.dl-item>
                    @endif
                    <x-ui.dl-item label="Verwendungszweck" num><span
                            class="select-all">{{ $booking->invoice_number ?? $booking->booking_number }}</span></x-ui.dl-item>
                </x-ui.dl>
                <p class="mt-4 mb-0 text-base text-ink-soft">
                    Sobald die Zahlung verbucht ist, erhalten Sie automatisch
                    {{ $event->isOnline() ? 'die Zugangsdaten' : ($booking->hasTicketDocument() ? 'Ihre Tickets' : 'Ihre Bestätigung') }}
                    per E-Mail.
                </p>
                @if ($booking->invoice_number)
                    <x-ui.button variant="link" :href="route('bookings.invoice', $booking->booking_number)" arrow class="mt-2">
                        Rechnung herunterladen (PDF)
                    </x-ui.button>
                @endif
            @endif
        @elseif($needsAttendees)
            <p class="m-0 text-h3">Bitte tragen Sie die Teilnehmenden ein.</p>
            <p class="mt-1 mb-0 text-base text-ink-soft">
                Sie haben {{ $booking->items->count() }} Plätze gebucht.
                @if ($isReady)
                    Danach senden wir Ihnen die personalisierten
                    {{ $booking->hasTicketDocument() ? 'Tickets' : 'Bestätigungen' }} sofort per E-Mail.
                @else
                    Die {{ $booking->hasTicketDocument() ? 'Tickets' : 'Zugangsdaten' }} erhalten Sie nach Bestätigung
                    bzw. Zahlungseingang.
                @endif
            </p>
            <x-ui.button :href="route('bookings.personalize', $booking->booking_number)" class="mt-4 w-full sm:w-auto">
                Teilnehmende eintragen
            </x-ui.button>
        @elseif($isReady && !$eventPast)
            <p class="m-0 text-h3">Alles erledigt – wir freuen uns auf Sie.</p>
            @if ($booking->ticketsReleasedBeforePayment())
                <p class="mt-1 mb-0 text-base text-ink-soft">Die Rechnung erhalten Sie separat vom Veranstalter –
                    gegebenenfalls auch erst nach der Veranstaltung.</p>
            @endif
            <p class="mt-1 mb-0 text-base text-ink-soft">
                @if ($event->requiresOnlineInfo())
                    Ihre Zugangsdaten finden Sie unten. Zusätzlich erhalten Sie sie 24 Stunden und 3 Stunden vor Beginn
                    per E-Mail.
                @elseif($booking->hasTicketDocument())
                    Bringen Sie Ihr Ticket (ausgedruckt oder auf dem Smartphone) mit. Wir erinnern Sie 24 Stunden vor
                    Beginn per E-Mail.
                @else
                    Ihre Buchungsnummer genügt als Nachweis. Wir erinnern Sie 24 Stunden vor Beginn per E-Mail.
                @endif
            </p>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                @if ($booking->canAccessOnlineContent() && $event->online_url)
                    <x-ui.button :href="$event->online_url" target="_blank" rel="noopener">
                        <x-slot:icon><x-icon.external-link /></x-slot:icon>
                        Zur Online-Veranstaltung
                    </x-ui.button>
                @endif
                @if ($booking->hasTicketDocument() && $booking->canSendTickets())
                    <x-ui.button :href="route('bookings.ticket', $booking->booking_number)"
                        :variant="$booking->canAccessOnlineContent() && $event->online_url ? 'secondary' : 'primary'">
                        <x-slot:icon><x-icon.download /></x-slot:icon>
                        Ticket herunterladen (PDF)
                    </x-ui.button>
                @endif
                <x-ui.button variant="secondary" :href="route('bookings.ical', $booking->booking_number)">
                    <x-slot:icon><x-icon.calendar /></x-slot:icon>
                    In Kalender eintragen
                </x-ui.button>
            </div>
        @elseif($eventPast)
            <p class="m-0 text-h3">Die Veranstaltung ist beendet. Vielen Dank für Ihre Teilnahme.</p>
            @if ($isReady)
                <p class="mt-1 mb-0 text-base text-ink-soft">Teilnahmezertifikate erhalten eingecheckte Teilnehmende
                    unten bei den Tickets.</p>
            @endif
        @else
            <p class="m-0 text-h3">Ihre Buchung wird bearbeitet.</p>
            <p class="mt-1 mb-0 text-base text-ink-soft">Sie erhalten eine E-Mail, sobald sich etwas ändert.</p>
        @endif
    </x-ui.box>
@endunless

{{-- Hinweis Konto für Gäste --}}
@if (!auth()->check() && !$booking->user_id && session()->has('booking_access_' . $booking->id) && !$isCancelled)
    @php $existingUser = \App\Models\User::where('email', $booking->customer_email)->exists(); @endphp
    <x-ui.alert tone="info" class="mb-8">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p>
                @if ($existingUser)
                    <strong class="font-semibold">Mit Ihrem Konto verknüpfen:</strong> So finden Sie diese Buchung
                    jederzeit unter „Meine Buchungen“.
                @else
                    <strong class="font-semibold">Tipp:</strong> Mit einem kostenlosen Konto haben Sie alle Buchungen,
                    Tickets und Zertifikate an einem Ort.
                @endif
            </p>
            <x-ui.button variant="secondary" size="sm" :href="route('bookings.create-account', $booking->booking_number)"
                class="self-start no-underline! sm:self-auto">
                {{ $existingUser ? 'Jetzt verknüpfen' : 'Konto erstellen' }}
            </x-ui.button>
        </div>
    </x-ui.alert>
@endif

<div class="grid items-start gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,368px)] lg:gap-x-12">
    <div class="flex min-w-0 flex-col gap-10">
        {{-- Veranstaltung --}}
        <x-ui.section title="Veranstaltung" id="abschnitt-veranstaltung" class="[&>div:first-child]:mb-4">
            <div class="flex items-center gap-x-5 gap-y-4">
                <x-ui.date-block :date="$event->start_date" :end="$event->end_date" />
                <div class="flex min-w-0 flex-col gap-1">
                    @if ($event->category)
                        <span class="text-small text-muted">{{ $event->category->name }}</span>
                    @endif
                    <a href="{{ route('events.show', $event->slug) }}"
                        class="text-h3 text-ink no-underline underline-offset-[3px] hover:underline">{{ $event->title }}</a>
                    <span class="flex flex-wrap gap-x-4 gap-y-0.5 text-small text-ink-soft tabular-nums">
                        <span>{{ Ui::dateTime($event->start_date, $event->end_date) }}</span>
                        <span>{{ Ui::formatAndPlace($event) }}</span>
                    </span>
                </div>
            </div>
            @if ($event->requiresVenue() && ($event->venue_name || $event->location))
                <p class="mt-4 mb-0 flex items-start gap-2 text-base">
                    <x-icon.location class="mt-1 h-4 w-4 flex-none text-muted" />
                    <span>{{ implode(', ', array_filter([$event->venue_name, $event->venue_address, $event->venue_city ? trim($event->venue_postal_code . ' ' . $event->venue_city) : null])) }}</span>
                </p>
            @endif

            {{-- Online-Zugang (Online & Hybrid) --}}
            @if ($event->requiresOnlineInfo() && !$isCancelled)
                @if ($booking->canAccessOnlineContent() && $event->online_url)
                    <x-ui.alert tone="info" title="Online-Zugang" class="mt-4">
                        <p><a href="{{ $event->online_url }}" target="_blank" rel="noopener"
                                class="break-all">{{ $event->online_url }}</a></p>
                        @if ($event->online_access_code)
                            <p class="mt-1">Zugangscode:
                                <code
                                    class="rounded-[4px] border border-line bg-surface px-2 py-0.5 tabular-nums select-all">{{ $event->online_access_code }}</code>
                            </p>
                        @endif
                    </x-ui.alert>
                @else
                    <x-ui.alert tone="info" class="mt-4">
                        Die Online-Zugangsdaten werden hier und per E-Mail freigeschaltet, sobald Ihre Buchung
                        {{ $booking->status === 'pending_approval' ? 'bestätigt' : 'bestätigt und bezahlt' }} ist.
                    </x-ui.alert>
                @endif
            @endif

            @if ($event->ticket_notes && $isReady)
                <x-ui.alert tone="warning" title="Hinweise des Veranstalters" class="mt-4">
                    <p>{!! nl2br(e($event->ticket_notes)) !!}</p>
                </x-ui.alert>
            @endif

            <x-ui.button variant="link" :href="route('events.show', $event->slug)" arrow class="mt-2">
                Veranstaltungsdetails
            </x-ui.button>
        </x-ui.section>

        {{-- Tickets / Teilnehmende --}}
        <x-ui.section :title="$booking->items->count() > 1 ? 'Plätze & Teilnehmende' : 'Ihr Platz'" id="abschnitt-plaetze"
            class="[&>div:first-child]:mb-4">
            @if ($booking->canBePersonalized() && !$readOnly)
                <x-slot:actions>
                    <x-ui.button variant="secondary" size="sm"
                        :href="route('bookings.personalize', $booking->booking_number)">
                        {{ $booking->tickets_personalized ? 'Teilnehmende ändern' : 'Teilnehmende eintragen' }}
                    </x-ui.button>
                </x-slot:actions>
            @endif
            <x-ui.table label="Gebuchte Plätze">
                <thead>
                    <tr>
                        <th scope="col">Ticket</th>
                        <th scope="col">Teilnehmer:in</th>
                        @unless ($isFree)
                            <th scope="col" class="num max-sm:hidden">Preis</th>
                        @endunless
                        @if ($eventPast)
                            <th scope="col" class="actions"><span class="sr-only">Zertifikat</span></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($booking->items as $item)
                        <tr>
                            <th scope="row">
                                {{ $item->ticketType?->name ?? 'Ticket' }}
                                <span
                                    class="block text-caption font-normal whitespace-nowrap text-muted tabular-nums">{{ $item->ticket_number }}</span>
                                @unless ($isFree)
                                    <span
                                        class="block text-small font-normal text-muted tabular-nums sm:hidden">{{ Ui::money($item->price) }}</span>
                                @endunless
                            </th>
                            <td>
                                @if ($item->attendee_name)
                                    {{ $item->attendee_name }}
                                    @if ($item->attendee_email)
                                        <span class="block text-small [overflow-wrap:anywhere] text-muted">{{ $item->attendee_email }}</span>
                                    @endif
                                @else
                                    <x-ui.status tone="warning">Teilnehmer:in noch nicht eingetragen</x-ui.status>
                                @endif
                                @if ($item->checked_in && $item->checked_in_at)
                                    <x-ui.status tone="success" class="mt-1 flex">Eingecheckt am
                                        {{ $item->checked_in_at->format('d.m.Y H:i') }} Uhr</x-ui.status>
                                @endif
                            </td>
                            @unless ($isFree)
                                <td class="num max-sm:hidden">{{ Ui::money($item->price) }}</td>
                            @endunless
                            @if ($eventPast)
                                <td class="actions">
                                    @if ($item->checked_in)
                                        <x-ui.button variant="link" size="sm"
                                            :href="route('bookings.certificate.individual', ['bookingNumber' => $booking->booking_number, 'itemId' => $item->id])">
                                            Zertifikat
                                        </x-ui.button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        </x-ui.section>

        {{-- Kundendaten --}}
        <x-ui.section title="Ihre Angaben" id="abschnitt-angaben" class="[&>div:first-child]:mb-4">
            <x-ui.dl>
                <x-ui.dl-item label="Name">{{ $booking->customer_name }}</x-ui.dl-item>
                <x-ui.dl-item label="E-Mail"><span class="break-all">{{ $booking->customer_email }}</span></x-ui.dl-item>
                @if ($booking->customer_phone)
                    <x-ui.dl-item label="Telefon" num>{{ $booking->customer_phone }}</x-ui.dl-item>
                @endif
                @if ($booking->customer_organization)
                    <x-ui.dl-item label="Einrichtung">{{ $booking->customer_organization }}</x-ui.dl-item>
                @endif
                @if ($booking->billing_company)
                    <x-ui.dl-item label="Firma (Rechnung)">{{ $booking->billing_company }}</x-ui.dl-item>
                @endif
                @if ($booking->billing_vat_id)
                    <x-ui.dl-item label="USt-IdNr.">{{ $booking->billing_vat_id }}</x-ui.dl-item>
                @endif
            </x-ui.dl>
        </x-ui.section>

        {{-- Feedback nach der Veranstaltung --}}
        @if ($eventPast && $isReady && (!$readOnly || $booking->review))
            <section id="feedback" aria-labelledby="feedback-titel" class="border-t border-line pt-8">
                @if ($booking->review)
                    <h2 id="feedback-titel" class="m-0 text-h3">Danke für Ihr Feedback</h2>
                    <p class="mt-2 mb-0 flex items-center gap-2 text-base text-ink-soft">
                        Ihre Bewertung:
                        <span class="flex gap-0.5 text-ink" aria-hidden="true">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $booking->review->rating)
                                    <x-icon.star-filled class="h-5 w-5" />
                                @else
                                    <x-icon.star class="h-5 w-5 text-line-strong" />
                                @endif
                            @endfor
                        </span>
                        <span class="sr-only">{{ $booking->review->rating }} von 5 Sternen</span>
                    </p>
                @else
                    <h2 id="feedback-titel" class="m-0 text-h2">Wie hat Ihnen die Veranstaltung gefallen?</h2>
                    <p class="mt-1 mb-0 text-base text-muted">Ihr Feedback hilft dem Veranstalter, zukünftige Angebote zu
                        verbessern.</p>
                    <form method="POST" action="{{ route('bookings.feedback', $booking->booking_number) }}"
                        class="mt-6 flex max-w-form flex-col gap-5" x-data="{ rating: {{ (int) old('rating', 0) }} }">
                        @csrf
                        <fieldset class="m-0 min-w-0 border-0 p-0">
                            <x-ui.label as="legend" required>Bewertung</x-ui.label>
                            <div class="flex gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <label
                                        class="inline-flex h-11 w-11 cursor-pointer items-center justify-center rounded-control has-focus-visible:outline-2 has-focus-visible:outline-offset-2 has-focus-visible:outline-ink">
                                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only"
                                            x-model.number="rating" @checked(old('rating') == $i)>
                                        <span aria-hidden="true" :class="rating >= {{ $i }} ? 'text-ink' : 'text-line-strong'"
                                            class="text-line-strong transition-colors duration-150">
                                            <x-icon.star-filled class="h-7 w-7" />
                                        </span>
                                        <span class="sr-only">{{ $i }} von 5 Sternen</span>
                                    </label>
                                @endfor
                            </div>
                            <x-ui.field-error name="rating" />
                        </fieldset>
                        <x-ui.textarea label="Kommentar" name="comment" id="feedback-comment" optional rows="3"
                            maxlength="1000" placeholder="Was war besonders hilfreich? Was können wir verbessern?" />
                        <div>
                            <x-ui.button type="submit">Feedback senden</x-ui.button>
                        </div>
                    </form>
                @endif
            </section>
        @endif
    </div>

    {{-- Seitenleiste --}}
    <div class="flex min-w-0 flex-col gap-8">
        <x-ui.summary :sticky="false" id="buchung-zusammenfassung">
            <div class="flex flex-col gap-2">
                <x-ui.summary-line label="Plätze" :amount="$booking->items->count()" />
                @if ($isFree)
                    <x-ui.summary-line label="Kosten" amount="kostenfrei" />
                @else
                    <x-ui.summary-line label="Zwischensumme" :amount="Ui::money($booking->subtotal)" />
                    @if ($booking->discount > 0)
                        <x-ui.summary-line label="Rabatt" :amount="'−' . Ui::money($booking->discount)" muted />
                    @endif
                @endif
            </div>
            @unless ($isFree)
                <x-ui.summary-total :amount="Ui::money($booking->total)" />
                <div class="flex flex-col gap-2">
                    <x-ui.summary-line label="Zahlart"
                        :amount="$booking->payment_method === 'paypal' ? 'PayPal' : 'Rechnung'" />
                    @if ($booking->ticketsReleasedBeforePayment())
                        <x-ui.summary-line label="Rechnung" amount="folgt separat" />
                    @elseif($booking->payment_status !== 'extern')
                        <x-ui.summary-line label="Zahlung">
                            <x-ui.status :tone="Ui::paymentTone($booking->payment_status)">{{ $booking->payment_status === 'pending' ? 'Offen' : $booking->paymentStatusLabel() }}</x-ui.status>
                        </x-ui.summary-line>
                    @endif
                </div>
            @endunless
            <p class="m-0 text-small text-muted tabular-nums">Gebucht am {{ $booking->created_at->format('d.m.Y H:i') }}
                Uhr</p>
        </x-ui.summary>

        <section aria-labelledby="dokumente-titel" class="flex flex-col gap-3">
            <h2 id="dokumente-titel" @class(['m-0 mb-1 text-h3', 'max-sm:hidden' => $isCancelled && !$canCancelOnline])>Dokumente &amp; Aktionen</h2>
            @if ($booking->hasTicketDocument() && $isReady && $booking->canSendTickets())
                <x-ui.button :href="route('bookings.ticket', $booking->booking_number)" full>
                    <x-slot:icon><x-icon.ticket /></x-slot:icon>
                    Ticket (PDF)
                </x-ui.button>
            @endif
            @if (!$isFree && !$isCancelled && !$isExternalInvoicing && $booking->invoice_number)
                <x-ui.button variant="secondary" :href="route('bookings.invoice', $booking->booking_number)" full>
                    <x-slot:icon><x-icon.receipt /></x-slot:icon>
                    Rechnung herunterladen (PDF)
                </x-ui.button>
            @elseif(!$isFree && $isExternalInvoicing)
                <p class="m-0 text-small text-muted">Die Rechnung erhalten Sie separat vom Veranstalter.</p>
            @endif
            @if ($isReady && $eventPast)
                <x-ui.button variant="secondary" :href="route('bookings.certificate', $booking->booking_number)" full>
                    <x-slot:icon><x-icon.medal /></x-slot:icon>
                    Teilnahmezertifikat
                </x-ui.button>
            @endif
            @if (!$isCancelled && !$eventPast)
                <x-ui.button variant="secondary" :href="route('bookings.ical', $booking->booking_number)" full>
                    <x-slot:icon><x-icon.calendar /></x-slot:icon>
                    Kalendereintrag (.ics)
                </x-ui.button>
            @endif
            <x-ui.button variant="link" onclick="window.print()" class="self-start max-sm:hidden">Seite
                drucken</x-ui.button>

            @if ($canCancelOnline)
                <div class="mt-2 flex flex-col gap-2 border-t border-line pt-4">
                    @if ($event->canCancelBooking())
                        <x-ui.modal name="cancel-booking" title="Buchung stornieren?">
                            <x-slot:trigger>
                                <x-ui.button variant="secondary" full>Buchung stornieren</x-ui.button>
                            </x-slot:trigger>
                            <p class="m-0">Möchten Sie diese Buchung wirklich stornieren? Dies kann nicht rückgängig
                                gemacht werden.</p>
                            <form id="cancel-booking-form" method="POST"
                                action="{{ route('bookings.cancel', $booking->booking_number) }}" class="hidden">
                                @csrf
                            </form>
                            <x-slot:footer>
                                <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
                                <x-ui.button variant="danger" type="submit" form="cancel-booking-form">Buchung
                                    stornieren</x-ui.button>
                            </x-slot:footer>
                        </x-ui.modal>
                        @if ($event->cancellation_days_before !== null)
                            <p class="m-0 text-center text-small text-muted tabular-nums">Kostenfrei möglich bis
                                {{ $event->start_date->copy()->subDays($event->cancellation_days_before)->format('d.m.Y') }}
                            </p>
                        @endif
                    @elseif($event->cancellation_allowed && $event->cancellation_days_before !== null)
                        <p class="m-0 text-small text-muted">Die Stornierungsfrist ist abgelaufen (bis
                            {{ $event->cancellation_days_before }} Tag(e) vor Beginn). Bitte wenden Sie sich an den
                            Veranstalter.</p>
                    @else
                        <p class="m-0 text-small text-muted">Eine Online-Stornierung ist für diese Veranstaltung nicht
                            möglich. Bitte wenden Sie sich an den Veranstalter.</p>
                    @endif
                </div>
            @endif
        </section>

        <section aria-labelledby="fragen-titel" class="flex flex-col gap-1 border-t border-line pt-6">
            <h2 id="fragen-titel" class="m-0 mb-1 text-h3">Fragen zur Buchung?</h2>
            <p class="m-0 text-base">{{ $event->getOrganizerName() }}</p>
            @if ($organizerEmail)
                <a href="mailto:{{ $organizerEmail }}?subject={{ rawurlencode('Buchung ' . $booking->booking_number) }}"
                    class="text-base font-medium break-all text-ink underline underline-offset-[3px]">{{ $organizerEmail }}</a>
            @endif
            @if ($organizerPhone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $organizerPhone) }}"
                    class="text-base font-medium text-ink underline underline-offset-[3px] tabular-nums">{{ $organizerPhone }}</a>
            @endif
        </section>
    </div>
</div>
