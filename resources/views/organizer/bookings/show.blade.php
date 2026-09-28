@php
    $event = $booking->event;
    $organization = $event->organization;
    $isExternalInvoicing = $organization?->hasExternalInvoicing() ?? false;
    $isCancelled = $booking->status === 'cancelled';
    $isReady = $booking->isReadyForParticipation();
    $deliverable = $event->isOnline() ? 'Zugangsdaten' : ($booking->hasTicketDocument() ? ($event->requiresOnlineInfo() ? 'Tickets und Zugangsdaten' : 'Tickets') : 'Bestätigung');

    $statusBadge = match ($booking->status) {
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
        'pending_approval' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
        'confirmed' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
        'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
        default => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
    };
    $paymentBadge = match ($booking->payment_status) {
        'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200',
        'extern' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200',
        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200',
        'refunded' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        default => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200',
    };
    $card = 'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6';
    $btnPrimary = 'inline-flex justify-center items-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white transition';
    $btnSecondary = 'inline-flex justify-center items-center px-4 py-2.5 rounded-lg text-sm font-medium border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition';
@endphp

<x-layouts.app title="Buchungsdetails">
    <div class="py-4 sm:py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <div>
                <a href="{{ route('organizer.bookings.index', ['event_id' => $event->id]) }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">← Buchungen dieser Veranstaltung</a>
                <div class="mt-3 flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $booking->customer_name }}</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-mono">{{ $booking->booking_number }}</span> · gebucht am {{ $booking->created_at->format('d.m.Y H:i') }} Uhr
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $statusBadge }}">{{ $booking->statusLabel() }}</span>
                        @if(!$booking->isFree() && !($booking->status === 'pending' && $booking->payment_status === 'pending'))
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $paymentBadge }}">{{ $booking->paymentStatusLabel() }}</span>
                        @endif
                    </div>
                </div>
            </div>

            @foreach([
                'success' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200',
                'status' => 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200',
                'info' => 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200',
                'warning' => 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-200',
                'error' => 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200',
            ] as $key => $classes)
                @if(session($key))
                    <div class="{{ $classes }} border px-4 py-3 rounded-lg text-sm" role="{{ $key === 'error' ? 'alert' : 'status' }}">{{ session($key) }}</div>
                @endif
            @endforeach
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm" role="alert">{{ $errors->first() }}</div>
            @endif

            {{-- Fortschritt --}}
            <div class="{{ $card }}">
                <x-booking-progress :booking="$booking" />
            </div>

            {{-- Nächster Schritt für den Veranstalter --}}
            @unless($isCancelled)
                <div class="rounded-xl border-2 p-4 sm:p-5
                    {{ $isReady && ($booking->tickets_personalized || $booking->items->count() <= 1) ? 'border-green-200 bg-green-50 dark:bg-green-900/20 dark:border-green-800' : 'border-amber-200 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800' }}">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-600 dark:text-gray-300 mb-2">Nächster Schritt</h2>

                    @if($booking->status === 'pending_approval')
                        <p class="font-medium text-gray-900 dark:text-gray-100">Diese Anmeldung wartet auf Ihre Freigabe.</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Bei Bestätigung erhält die Person sofort die Buchungsbestätigung mit {{ $deliverable }}. Bei Ablehnung eine Absage (optional mit Begründung).</p>
                        <div class="mt-4 flex flex-col sm:flex-row gap-2" x-data="{ reject: false }">
                            <form method="POST" action="{{ route('organizer.bookings.approve', $booking) }}">
                                @csrf
                                <button type="submit" class="{{ $btnPrimary }} w-full sm:w-auto bg-green-600 hover:bg-green-700">✓ Anmeldung bestätigen</button>
                            </form>
                            <button type="button" @click="reject = !reject" class="{{ $btnSecondary }} text-red-700 dark:text-red-400">✕ Ablehnen…</button>
                            <form x-show="reject" x-cloak method="POST" action="{{ route('organizer.bookings.reject', $booking) }}" class="w-full sm:flex-1 space-y-2">
                                @csrf
                                <textarea name="rejection_reason" rows="2" placeholder="Begründung für die Person (optional)"
                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm"></textarea>
                                <button type="submit" class="{{ $btnPrimary }} bg-red-600 hover:bg-red-700">Ablehnung senden</button>
                            </form>
                        </div>

                    @elseif(!$booking->isPaymentComplete() && !$booking->isFree() && $isReady)
                        {{-- Tickets wurden vor Zahlung/Rechnung freigegeben --}}
                        <p class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $deliverable }} bereits versendet – {{ $isExternalInvoicing ? 'Rechnung noch stellen' : 'Zahlung noch offen' }}.
                        </p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            {{ $isExternalInvoicing ? 'Stellen Sie die Rechnung in Ihrer Buchhaltung (auch nach der Veranstaltung möglich) und markieren Sie die Buchung anschließend als fakturiert.' : 'Verbuchen Sie die Zahlung, sobald sie eingegangen ist.' }}
                            @if($booking->needsPersonalization()) Die Person muss außerdem noch die Teilnehmenden eintragen. @endif
                        </p>
                        @if($isExternalInvoicing)
                            <form action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}" method="POST" class="mt-4 flex flex-col sm:flex-row gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="external_invoice_number" placeholder="Externe Rechnungsnr. (optional)"
                                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <button type="submit" class="{{ $btnPrimary }} bg-blue-600 hover:bg-blue-700">Als fakturiert markieren</button>
                            </form>
                        @else
                            <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="mt-4">
                                @csrf @method('PUT')
                                <input type="hidden" name="payment_status" value="paid">
                                <button type="submit" class="{{ $btnPrimary }} w-full sm:w-auto bg-green-600 hover:bg-green-700">✓ Zahlung eingegangen</button>
                            </form>
                        @endif

                    @elseif(!$booking->isPaymentComplete() && !$booking->isFree())
                        @if($isExternalInvoicing)
                            <p class="font-medium text-gray-900 dark:text-gray-100">Rechnung extern stellen und hier als fakturiert markieren.</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Danach wird die Buchung bestätigt und die Person erhält automatisch {{ $deliverable }}.</p>
                            <form action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}" method="POST" class="mt-4 flex flex-col sm:flex-row gap-2">
                                @csrf @method('PUT')
                                <input type="text" name="external_invoice_number" placeholder="Externe Rechnungsnr. (optional)"
                                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <button type="submit" class="{{ $btnPrimary }} bg-blue-600 hover:bg-blue-700">Als fakturiert markieren & {{ $deliverable }} senden</button>
                            </form>
                        @elseif($booking->payment_method === 'paypal')
                            <p class="font-medium text-gray-900 dark:text-gray-100">Die Person hat die PayPal-Zahlung noch nicht abgeschlossen.</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                                Nach erfolgreicher Zahlung wird die Buchung automatisch bestätigt und {{ $deliverable }} versendet.
                                Nicht abgeschlossene PayPal-Zahlungen werden nach 48 Stunden automatisch storniert und die Plätze freigegeben.
                            </p>
                        @else
                            <p class="font-medium text-gray-900 dark:text-gray-100">Zahlung offen: {{ number_format($booking->total, 2, ',', '.') }} €</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Verwendungszweck: <span class="font-mono">{{ $booking->invoice_number ?? $booking->booking_number }}</span>. Sobald Sie den Zahlungseingang verbuchen, wird die Buchung bestätigt und die Person erhält automatisch {{ $deliverable }}.</p>
                            <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="mt-4"
                                  onsubmit="return confirm('Zahlungseingang verbuchen? Die Person erhält daraufhin {{ $deliverable }} per E-Mail.')">
                                @csrf @method('PUT')
                                <input type="hidden" name="payment_status" value="paid">
                                <button type="submit" class="{{ $btnPrimary }} w-full sm:w-auto bg-green-600 hover:bg-green-700">✓ Zahlung eingegangen – {{ $deliverable }} senden</button>
                            </form>
                        @endif

                        @if($booking->payment_method !== 'paypal' && in_array($booking->status, ['pending', 'confirmed']))
                            <form action="{{ route('organizer.bookings.release-tickets', $booking) }}" method="POST" class="mt-3"
                                  onsubmit="return confirm('{{ $deliverable }} jetzt senden, obwohl {{ $isExternalInvoicing ? 'die Rechnung noch nicht gestellt' : 'die Zahlung noch nicht eingegangen' }} ist?')">
                                @csrf
                                <button type="submit" class="{{ $btnSecondary }} w-full sm:w-auto">
                                    {{ $deliverable }} vorab senden – {{ $isExternalInvoicing ? 'Rechnung folgt' : 'Zahlung folgt' }} später
                                </button>
                            </form>
                        @endif

                    @elseif(!$isReady)
                        <p class="font-medium text-gray-900 dark:text-gray-100">Buchung bestätigen</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Die Zahlung ist erledigt, die Buchung aber noch nicht bestätigt.</p>
                        <form action="{{ route('organizer.bookings.update-status', $booking) }}" method="POST" class="mt-4">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="{{ $btnPrimary }} w-full sm:w-auto bg-green-600 hover:bg-green-700">Bestätigen & {{ $deliverable }} senden</button>
                        </form>

                    @elseif($booking->needsPersonalization())
                        <p class="font-medium text-gray-900 dark:text-gray-100">Die Person muss noch die Teilnehmenden eintragen.</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">Die Aufforderung wurde per E-Mail versendet. Nach dem Eintragen gehen {{ $deliverable }} automatisch raus. Sie können die Erinnerung erneut senden.</p>

                    @else
                        <p class="font-medium text-gray-900 dark:text-gray-100">Nichts zu tun – die Person hat {{ $deliverable }} erhalten.</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">
                            @if($event->start_date->isFuture())
                                Automatische Erinnerungen folgen 24 h und 3 h vor Beginn{{ $event->requiresOnlineInfo() ? ' (inkl. Zugangsdaten)' : '' }}.
                            @else
                                Die Veranstaltung hat bereits begonnen bzw. ist beendet.
                            @endif
                        </p>
                    @endif

                    @if($booking->status !== 'pending_approval')
                        <form action="{{ route('organizer.bookings.resend', $booking) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-blue-700 dark:text-blue-400 hover:underline">
                                ↻ Aktuelle Bestätigung erneut an {{ $booking->customer_email }} senden
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4 text-sm text-red-800 dark:text-red-200">
                    Diese Buchung ist storniert. Die Plätze wurden wieder freigegeben.
                    @if($booking->payment_status === 'paid' && !$booking->isFree())
                        <strong>Bitte veranlassen Sie die Erstattung von {{ number_format($booking->total, 2, ',', '.') }} €</strong> und setzen Sie den Zahlungsstatus anschließend auf „Erstattet“ – die Person wird dann automatisch informiert.
                        <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="mt-3">
                            @csrf @method('PUT')
                            <input type="hidden" name="payment_status" value="refunded">
                            <button type="submit" class="{{ $btnSecondary }}">Als erstattet markieren</button>
                        </form>
                    @endif
                </div>
            @endunless

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Veranstaltung --}}
                <section class="{{ $card }}">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Veranstaltung</h2>
                    <a href="{{ route('organizer.events.edit', $event) }}" class="text-base font-semibold text-gray-900 dark:text-gray-100 hover:underline">{{ $event->title }}</a>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $event->start_date->format('d.m.Y H:i') }} Uhr</p>
                    @if($event->requiresVenue() && $event->venue_name)
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $event->venue_name }}{{ $event->venue_city ? ', ' . $event->venue_city : '' }}</p>
                    @endif
                    <div class="mt-2"><x-event-type-badge :event="$event" /></div>
                </section>

                {{-- Kunde --}}
                <section class="{{ $card }}">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Gebucht von</h2>
                    <p class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $booking->customer_name }}</p>
                    @if($booking->customer_organization)
                        <p class="text-sm text-indigo-700 dark:text-indigo-300">{{ $booking->customer_organization }}</p>
                    @endif
                    <a href="mailto:{{ $booking->customer_email }}" class="block text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $booking->customer_email }}</a>
                    @if($booking->customer_phone)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $booking->customer_phone) }}" class="block text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ $booking->customer_phone }}</a>
                    @endif
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $booking->user_id ? 'Mit Benutzerkonto' : 'Gastbuchung' }}</p>
                    @if($booking->billing_company || $booking->billing_address)
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-sm text-gray-600 dark:text-gray-400">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Rechnungsadresse</p>
                            @if($booking->billing_company)<p>{{ $booking->billing_company }}</p>@endif
                            @if($booking->billing_address)<p>{{ $booking->billing_address }}, {{ $booking->billing_postal_code }} {{ $booking->billing_city }}</p>@endif
                            @if($booking->billing_vat_id)<p>USt-IdNr.: {{ $booking->billing_vat_id }}</p>@endif
                        </div>
                    @endif
                </section>
            </div>

            {{-- Tickets / Teilnehmende --}}
            <section class="{{ $card }}">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-3">Teilnehmende ({{ $booking->items->count() }})</h2>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($booking->items as $item)
                        <li class="py-3 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium {{ $item->attendee_name ? 'text-gray-900 dark:text-gray-100' : 'text-amber-700 dark:text-amber-400' }}">
                                    {{ $item->attendee_name ?? 'Noch nicht eingetragen' }}
                                </p>
                                @if($item->attendee_email)<p class="text-sm text-gray-600 dark:text-gray-400 break-all">{{ $item->attendee_email }}</p>@endif
                                @if($item->attendee_organization)<p class="text-sm text-indigo-700 dark:text-indigo-300">{{ $item->attendee_organization }}</p>@endif
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->ticketType?->name ?? 'Ticket' }} · <span class="font-mono">{{ $item->ticket_number }}</span></p>
                            </div>
                            <div class="text-right shrink-0 text-sm">
                                @unless($booking->isFree())
                                    <p class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($item->price, 2, ',', '.') }} €</p>
                                @endunless
                                @if($item->checked_in)
                                    <p class="text-green-700 dark:text-green-400">✓ {{ $item->checked_in_at?->format('d.m. H:i') }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
                @if(!$booking->isFree())
                    <dl class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 space-y-1 text-sm">
                        @if($booking->discount > 0)
                            <div class="flex justify-between text-green-700 dark:text-green-400"><dt>Rabatt @if($booking->discountCode)({{ $booking->discountCode->code }})@endif</dt><dd>−{{ number_format($booking->discount, 2, ',', '.') }} €</dd></div>
                        @endif
                        <div class="flex justify-between font-bold text-gray-900 dark:text-gray-100"><dt>Gesamt</dt><dd>{{ number_format($booking->total, 2, ',', '.') }} €</dd></div>
                        <div class="flex justify-between text-gray-600 dark:text-gray-400"><dt>Zahlart</dt><dd>{{ $booking->payment_method === 'paypal' ? 'PayPal' : 'Rechnung' }}@if($booking->invoice_number) · {{ $booking->invoice_number }}@endif</dd></div>
                        @if($isExternalInvoicing)
                            <div class="flex justify-between text-gray-600 dark:text-gray-400"><dt>Extern fakturiert</dt>
                                <dd>@if($booking->externally_invoiced) ✓ Fakturiert @if($booking->external_invoice_number)({{ $booking->external_invoice_number }})@endif @else ⏳ Noch nicht fakturiert @endif</dd></div>
                        @endif
                    </dl>
                    @if($isExternalInvoicing && !$booking->externally_invoiced && !$isCancelled && $booking->isPaymentComplete())
                        {{-- Bereits bezahlte/bestätigte Buchungen: nur buchhalterisch als fakturiert markieren --}}
                        <form action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}" method="POST" class="mt-3 flex flex-col sm:flex-row gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="external_invoice_number" placeholder="Externe Rechnungsnr. (optional)"
                                   class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                            <button type="submit" class="{{ $btnSecondary }}">Als fakturiert markieren</button>
                        </form>
                    @endif
                @endif
            </section>

            {{-- E-Mail-Verlauf --}}
            <section class="{{ $card }}">
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-1">E-Mail-Verlauf</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Alle E-Mails, die zu dieser Buchung automatisch oder durch Sie versendet wurden.</p>
                @if($booking->emailLogs->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">Noch keine E-Mails protokolliert.</p>
                @else
                    <ol class="relative border-l border-gray-200 dark:border-gray-700 ml-2 space-y-4">
                        @foreach($booking->emailLogs as $log)
                            <li class="ml-4">
                                <span class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full border border-white dark:border-gray-800 bg-blue-500"></span>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $log->typeLabel() }}</p>
                                <p class="text-xs text-gray-600 dark:text-gray-400">{{ $log->sent_at->format('d.m.Y H:i') }} Uhr · an <span class="break-all">{{ $log->recipient }}</span></p>
                                @if($log->subject)<p class="text-xs text-gray-500 dark:text-gray-500 italic">„{{ $log->subject }}“</p>@endif
                            </li>
                        @endforeach
                    </ol>
                @endif
            </section>

            {{-- Weitere Aktionen --}}
            <section class="{{ $card }}" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="w-full flex items-center justify-between text-left" :aria-expanded="open">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Weitere Aktionen</h2>
                    <span class="text-gray-500" x-text="open ? '▲' : '▼'"></span>
                </button>
                <div x-show="open" x-cloak class="mt-4 space-y-6">
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('bookings.show', $booking->booking_number) }}" target="_blank" class="{{ $btnSecondary }}">Kundenansicht öffnen</a>
                        @if($booking->hasTicketDocument() && $isReady)
                            <a href="{{ route('bookings.ticket', $booking->booking_number) }}" class="{{ $btnSecondary }}">Tickets (PDF)</a>
                        @endif
                        @if(!$booking->isFree() && !$isExternalInvoicing && $booking->invoice_number)
                            <a href="{{ route('bookings.invoice', $booking->booking_number) }}" class="{{ $btnSecondary }}">Rechnung (PDF)</a>
                        @endif
                    </div>

                    @if($booking->isActive() && $booking->status !== 'completed')
                        <form action="{{ route('organizer.bookings.cancel', $booking) }}" method="POST" class="space-y-2 border-t border-gray-100 dark:border-gray-700 pt-4"
                              onsubmit="return confirm('Buchung wirklich stornieren? Die Plätze werden freigegeben.')">
                            @csrf
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Buchung stornieren</h3>
                            <textarea name="cancellation_reason" rows="2" placeholder="Grund (wird der Person mitgeteilt, optional)"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm"></textarea>
                            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <input type="hidden" name="notify_customer" value="0">
                                <input type="checkbox" name="notify_customer" value="1" checked class="rounded border-gray-300">
                                Stornobestätigung per E-Mail an die Person senden
                            </label>
                            <button type="submit" class="{{ $btnPrimary }} bg-red-600 hover:bg-red-700">Stornieren</button>
                        </form>
                    @endif

                    @unless($isCancelled)
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Status manuell setzen</h3>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                „Bezahlt“{{ $isExternalInvoicing ? ' bzw. „Extern fakturiert“' : '' }} bestätigt die Buchung und sendet {{ $deliverable }}.
                                „Erstattet“/„Fehlgeschlagen“ informieren die Person per E-Mail. Übrige Änderungen erfolgen ohne E-Mail.
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <form action="{{ route('organizer.bookings.update-status', $booking) }}" method="POST" class="flex gap-2">
                                    @csrf @method('PUT')
                                    <label class="sr-only" for="status-select">Buchungsstatus</label>
                                    <select id="status-select" name="status" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                        @foreach(['pending' => 'Ausstehend', 'pending_approval' => 'Wartet auf Freigabe', 'confirmed' => 'Bestätigt', 'completed' => 'Abgeschlossen'] as $value => $label)
                                            <option value="{{ $value }}" @selected($booking->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="{{ $btnSecondary }}">Setzen</button>
                                </form>
                                @unless($booking->isFree())
                                    <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="flex gap-2">
                                        @csrf @method('PUT')
                                        <label class="sr-only" for="payment-select">Zahlungsstatus</label>
                                        <select id="payment-select" name="payment_status" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                            <option value="pending" @selected($booking->payment_status === 'pending')>Zahlung ausstehend</option>
                                            <option value="paid" @selected($booking->payment_status === 'paid')>Bezahlt</option>
                                            @if($isExternalInvoicing)
                                                <option value="extern" @selected($booking->payment_status === 'extern')>Extern fakturiert</option>
                                            @endif
                                            <option value="refunded" @selected($booking->payment_status === 'refunded')>Erstattet</option>
                                            <option value="failed" @selected($booking->payment_status === 'failed')>Fehlgeschlagen</option>
                                        </select>
                                        <button type="submit" class="{{ $btnSecondary }}">Setzen</button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    @endunless
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
