@php
    $event = $booking->event;
    $organization = $event->organization;
    $isExternalInvoicing = $organization?->hasExternalInvoicing() ?? false;
    $isCancelled = $booking->status === 'cancelled';
    $isReady = $booking->isReadyForParticipation();
    $deliverable = $event->isOnline() ? 'Zugangsdaten' : ($booking->hasTicketDocument() ? ($event->requiresOnlineInfo() ? 'Tickets und Zugangsdaten' : 'Tickets') : 'Bestätigung');
    $showPayment = !$booking->isFree() && !($booking->status === 'pending' && $booking->payment_status === 'pending');
@endphp

<x-layouts.app title="Buchungsdetails">
    <x-ui.page-header
        :title="$booking->booking_number"
        :subtitle="$booking->customer_name . ' · gebucht am ' . $booking->created_at->format('d.m.Y H:i') . ' Uhr'"
        :back-href="route('organizer.bookings.index', ['event_id' => $event->id])"
        back-label="Buchungen dieser Veranstaltung"
    >
        <x-slot:meta>
            <span class="flex flex-wrap items-center gap-x-4 gap-y-1">
                <x-ui.status :tone="\App\Support\Ui::bookingTone($booking->status)">{{ $booking->statusLabel() }}</x-ui.status>
                @if ($showPayment)
                    <x-ui.status :tone="\App\Support\Ui::paymentTone($booking->payment_status)">{{ $booking->paymentStatusLabel() }}</x-ui.status>
                @endif
            </span>
        </x-slot:meta>
    </x-ui.page-header>

    <div class="flex max-w-4xl flex-col gap-12">
        {{-- Fortschritt --}}
        <x-booking-progress :booking="$booking" />

        {{-- Nächster Schritt für den Veranstalter --}}
        @unless ($isCancelled)
            <x-ui.box tone="zone" padding="md">
                <h2 class="m-0 mb-3 text-h3">Nächster Schritt</h2>

                @if ($booking->status === 'pending_approval')
                    <p class="m-0 font-medium">Diese Anmeldung wartet auf Ihre Freigabe.</p>
                    <p class="m-0 mt-1 text-small text-ink-soft">Bei Bestätigung erhält die Person sofort die Buchungsbestätigung mit {{ $deliverable }}. Bei Ablehnung eine Absage (optional mit Begründung).</p>
                    <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-start" x-data="{ reject: false }">
                        <form method="POST" action="{{ route('organizer.bookings.approve', $booking) }}">
                            @csrf
                            <x-ui.button type="submit">Anmeldung bestätigen</x-ui.button>
                        </form>
                        <x-ui.button variant="secondary" @click="reject = !reject" x-bind:aria-expanded="reject.toString()">Ablehnen</x-ui.button>
                        <form x-show="reject" x-cloak method="POST" action="{{ route('organizer.bookings.reject', $booking) }}" class="flex w-full flex-col items-start gap-3">
                            @csrf
                            <x-ui.textarea label="Begründung für die Person" name="rejection_reason" optional rows="2" class="w-full" />
                            <x-ui.button type="submit" variant="danger">Ablehnung senden</x-ui.button>
                        </form>
                    </div>

                @elseif (!$booking->isPaymentComplete() && !$booking->isFree() && $isReady)
                    {{-- Tickets wurden vor Zahlung/Rechnung freigegeben --}}
                    <p class="m-0 font-medium">
                        {{ $deliverable }} bereits versendet – {{ $isExternalInvoicing ? 'Rechnung noch stellen' : 'Zahlung noch offen' }}.
                    </p>
                    <p class="m-0 mt-1 text-small text-ink-soft">
                        {{ $isExternalInvoicing ? 'Stellen Sie die Rechnung in Ihrer Buchhaltung (auch nach der Veranstaltung möglich) und markieren Sie die Buchung anschließend als fakturiert.' : 'Verbuchen Sie die Zahlung, sobald sie eingegangen ist.' }}
                        @if ($booking->needsPersonalization()) Die Person muss außerdem noch die Teilnehmenden eintragen. @endif
                    </p>
                    @if ($isExternalInvoicing)
                        <form action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}" method="POST" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                            @csrf @method('PUT')
                            <x-ui.field label="Externe Rechnungsnummer" name="external_invoice_number" optional class="sm:w-72" />
                            <x-ui.button type="submit">Als fakturiert markieren</x-ui.button>
                        </form>
                    @else
                        <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="mt-4">
                            @csrf @method('PUT')
                            <input type="hidden" name="payment_status" value="paid">
                            <x-ui.button type="submit">Zahlung eingegangen</x-ui.button>
                        </form>
                    @endif

                @elseif (!$booking->isPaymentComplete() && !$booking->isFree())
                    @if ($isExternalInvoicing)
                        <p class="m-0 font-medium">Rechnung extern stellen und hier als fakturiert markieren.</p>
                        <p class="m-0 mt-1 text-small text-ink-soft">Danach wird die Buchung bestätigt und die Person erhält automatisch {{ $deliverable }}.</p>
                        <form action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}" method="POST" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                            @csrf @method('PUT')
                            <x-ui.field label="Externe Rechnungsnummer" name="external_invoice_number" optional class="sm:w-72" />
                            <x-ui.button type="submit">Als fakturiert markieren &amp; {{ $deliverable }} senden</x-ui.button>
                        </form>
                    @elseif ($booking->payment_method === 'paypal')
                        <p class="m-0 font-medium">Die Person hat die PayPal-Zahlung noch nicht abgeschlossen.</p>
                        <p class="m-0 mt-1 text-small text-ink-soft">
                            Nach erfolgreicher Zahlung wird die Buchung automatisch bestätigt und {{ $deliverable }} versendet.
                            Nicht abgeschlossene PayPal-Zahlungen werden nach 48 Stunden automatisch storniert und die Plätze freigegeben.
                        </p>
                    @else
                        <p class="m-0 font-medium tabular-nums">Zahlung offen: {{ \App\Support\Ui::money($booking->total) }}</p>
                        <p class="m-0 mt-1 text-small text-ink-soft">Verwendungszweck: <span class="tabular-nums">{{ $booking->invoice_number ?? $booking->booking_number }}</span>. Sobald Sie den Zahlungseingang verbuchen, wird die Buchung bestätigt und die Person erhält automatisch {{ $deliverable }}.</p>
                        <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="mt-4"
                              onsubmit="return confirm('Zahlungseingang verbuchen? Die Person erhält daraufhin {{ $deliverable }} per E-Mail.')">
                            @csrf @method('PUT')
                            <input type="hidden" name="payment_status" value="paid">
                            <x-ui.button type="submit">Zahlung eingegangen – {{ $deliverable }} senden</x-ui.button>
                        </form>
                    @endif

                    @if ($booking->payment_method !== 'paypal' && in_array($booking->status, ['pending', 'confirmed']))
                        <form action="{{ route('organizer.bookings.release-tickets', $booking) }}" method="POST" class="mt-3"
                              onsubmit="return confirm('{{ $deliverable }} jetzt senden, obwohl {{ $isExternalInvoicing ? 'die Rechnung noch nicht gestellt' : 'die Zahlung noch nicht eingegangen' }} ist?')">
                            @csrf
                            <x-ui.button type="submit" variant="secondary">
                                {{ $deliverable }} vorab senden – {{ $isExternalInvoicing ? 'Rechnung folgt' : 'Zahlung folgt' }} später
                            </x-ui.button>
                        </form>
                    @endif

                @elseif (!$isReady)
                    <p class="m-0 font-medium">Buchung bestätigen</p>
                    <p class="m-0 mt-1 text-small text-ink-soft">Die Zahlung ist erledigt, die Buchung aber noch nicht bestätigt.</p>
                    <form action="{{ route('organizer.bookings.update-status', $booking) }}" method="POST" class="mt-4">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="confirmed">
                        <x-ui.button type="submit">Bestätigen &amp; {{ $deliverable }} senden</x-ui.button>
                    </form>

                @elseif ($booking->needsPersonalization())
                    <p class="m-0 font-medium">Die Person muss noch die Teilnehmenden eintragen.</p>
                    <p class="m-0 mt-1 text-small text-ink-soft">Die Aufforderung wurde per E-Mail versendet. Nach dem Eintragen gehen {{ $deliverable }} automatisch raus. Sie können die Erinnerung erneut senden.</p>

                @else
                    <p class="m-0 font-medium">Nichts zu tun – die Person hat {{ $deliverable }} erhalten.</p>
                    <p class="m-0 mt-1 text-small text-ink-soft">
                        @if ($event->start_date->isFuture())
                            Automatische Erinnerungen folgen 24 h und 3 h vor Beginn{{ $event->requiresOnlineInfo() ? ' (inkl. Zugangsdaten)' : '' }}.
                        @else
                            Die Veranstaltung hat bereits begonnen bzw. ist beendet.
                        @endif
                    </p>
                @endif

                @if ($booking->status !== 'pending_approval')
                    <form action="{{ route('organizer.bookings.resend', $booking) }}" method="POST" class="mt-3">
                        @csrf
                        <x-ui.button type="submit" variant="link">
                            <x-slot:icon><x-icon.mail /></x-slot:icon>
                            Aktuelle Bestätigung erneut an {{ $booking->customer_email }} senden
                        </x-ui.button>
                    </form>
                @endif
            </x-ui.box>
        @else
            <x-ui.alert tone="danger" size="base">
                <p class="m-0">Diese Buchung ist storniert. Die Plätze wurden wieder freigegeben.</p>
                @if ($booking->payment_status === 'paid' && !$booking->isFree())
                    <p class="m-0 mt-2"><strong class="font-semibold">Bitte veranlassen Sie die Erstattung von {{ \App\Support\Ui::money($booking->total) }}</strong> und setzen Sie den Zahlungsstatus anschließend auf „Erstattet“ – die Person wird dann automatisch informiert.</p>
                    <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="mt-3">
                        @csrf @method('PUT')
                        <input type="hidden" name="payment_status" value="refunded">
                        <x-ui.button type="submit" variant="secondary">Als erstattet markieren</x-ui.button>
                    </form>
                @endif
            </x-ui.alert>
        @endunless

        {{-- Veranstaltung --}}
        <x-ui.section title="Veranstaltung">
            <x-ui.dl>
                <x-ui.dl-item label="Veranstaltung">
                    <a href="{{ route('organizer.events.edit', $event) }}" class="font-medium text-ink underline underline-offset-[3px]">{{ $event->title }}</a>
                </x-ui.dl-item>
                <x-ui.dl-item label="Termin" num>{{ \App\Support\Ui::dateTime($event->start_date, $event->end_date) }}</x-ui.dl-item>
                <x-ui.dl-item label="Format"><x-event-type-badge :event="$event" size="md" /></x-ui.dl-item>
                @if ($event->requiresVenue() && $event->venue_name)
                    <x-ui.dl-item label="Ort">{{ $event->venue_name }}{{ $event->venue_city ? ', ' . $event->venue_city : '' }}</x-ui.dl-item>
                @endif
            </x-ui.dl>
        </x-ui.section>

        {{-- Kunde --}}
        <x-ui.section title="Gebucht von">
            <x-ui.dl>
                <x-ui.dl-item label="Name">{{ $booking->customer_name }}</x-ui.dl-item>
                @if ($booking->customer_organization)
                    <x-ui.dl-item label="Organisation">{{ $booking->customer_organization }}</x-ui.dl-item>
                @endif
                <x-ui.dl-item label="E-Mail">
                    <a href="mailto:{{ $booking->customer_email }}" class="break-all text-ink underline underline-offset-[3px]">{{ $booking->customer_email }}</a>
                </x-ui.dl-item>
                @if ($booking->customer_phone)
                    <x-ui.dl-item label="Telefon" num>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $booking->customer_phone) }}" class="text-ink underline underline-offset-[3px]">{{ $booking->customer_phone }}</a>
                    </x-ui.dl-item>
                @endif
                <x-ui.dl-item label="Konto">{{ $booking->user_id ? 'Mit Benutzerkonto' : 'Gastbuchung' }}</x-ui.dl-item>
                @if ($booking->billing_company || $booking->billing_address)
                    <x-ui.dl-item label="Rechnungsadresse">
                        @if ($booking->billing_company)<span class="block">{{ $booking->billing_company }}</span>@endif
                        @if ($booking->billing_address)<span class="block">{{ $booking->billing_address }}, {{ $booking->billing_postal_code }} {{ $booking->billing_city }}</span>@endif
                        @if ($booking->billing_vat_id)<span class="block">USt-IdNr.: {{ $booking->billing_vat_id }}</span>@endif
                    </x-ui.dl-item>
                @endif
            </x-ui.dl>
        </x-ui.section>

        {{-- Tickets / Teilnehmende --}}
        <x-ui.section title="Teilnehmende ({{ $booking->items->count() }})">
            <x-ui.table label="Teilnehmende und Tickets">
                <thead>
                    <tr>
                        <th scope="col">Teilnehmende</th>
                        <th scope="col">Ticket</th>
                        <th scope="col">Check-in</th>
                        @unless ($booking->isFree())
                            <th scope="col" class="num">Preis</th>
                        @endunless
                    </tr>
                </thead>
                <tbody>
                    @foreach ($booking->items as $item)
                        <tr>
                            <th scope="row">
                                @if ($item->attendee_name)
                                    {{ $item->attendee_name }}
                                @else
                                    <span class="text-warning">Noch nicht eingetragen</span>
                                @endif
                                @if ($item->attendee_email)<span class="block text-small font-normal text-muted break-all">{{ $item->attendee_email }}</span>@endif
                                @if ($item->attendee_organization)<span class="block text-small font-normal text-ink-soft">{{ $item->attendee_organization }}</span>@endif
                            </th>
                            <td>
                                {{ $item->ticketType?->name ?? 'Ticket' }}
                                <span class="block text-small text-muted tabular-nums">{{ $item->ticket_number }}</span>
                            </td>
                            <td>
                                @if ($item->checked_in)
                                    <x-ui.status tone="success" class="tabular-nums">Eingecheckt {{ $item->checked_in_at?->format('d.m. H:i') }}</x-ui.status>
                                @else
                                    <span class="text-muted">–</span>
                                @endif
                            </td>
                            @unless ($booking->isFree())
                                <td class="num">{{ \App\Support\Ui::money($item->price) }}</td>
                            @endunless
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>

            @if (!$booking->isFree())
                <x-ui.dl class="mt-6">
                    @if ($booking->discount > 0)
                        <x-ui.dl-item label="Rabatt{{ $booking->discountCode ? ' (' . $booking->discountCode->code . ')' : '' }}" num>−{{ \App\Support\Ui::money($booking->discount) }}</x-ui.dl-item>
                    @endif
                    <x-ui.dl-item label="Gesamt" num><strong class="font-semibold">{{ \App\Support\Ui::money($booking->total) }}</strong></x-ui.dl-item>
                    <x-ui.dl-item label="Zahlart">{{ $booking->payment_method === 'paypal' ? 'PayPal' : 'Rechnung' }}@if ($booking->invoice_number) · {{ $booking->invoice_number }}@endif</x-ui.dl-item>
                    @if ($isExternalInvoicing)
                        <x-ui.dl-item label="Extern fakturiert">
                            @if ($booking->externally_invoiced)
                                <x-ui.status tone="success">Fakturiert @if ($booking->external_invoice_number)({{ $booking->external_invoice_number }})@endif</x-ui.status>
                            @else
                                <x-ui.status tone="warning">Noch nicht fakturiert</x-ui.status>
                            @endif
                        </x-ui.dl-item>
                    @endif
                </x-ui.dl>
                @if ($isExternalInvoicing && !$booking->externally_invoiced && !$isCancelled && $booking->isPaymentComplete())
                    {{-- Bereits bezahlte/bestätigte Buchungen: nur buchhalterisch als fakturiert markieren --}}
                    <form action="{{ route('organizer.billing-data.mark-invoiced', $booking) }}" method="POST" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end">
                        @csrf @method('PUT')
                        <x-ui.field label="Externe Rechnungsnummer" name="external_invoice_number" optional class="sm:w-72" />
                        <x-ui.button type="submit" variant="secondary">Als fakturiert markieren</x-ui.button>
                    </form>
                @endif
            @endif
        </x-ui.section>

        {{-- E-Mail-Verlauf --}}
        <x-ui.section title="E-Mail-Verlauf" description="Alle E-Mails, die zu dieser Buchung automatisch oder durch Sie versendet wurden.">
            @if ($booking->emailLogs->isEmpty())
                <p class="m-0 text-base text-muted">Noch keine E-Mails protokolliert.</p>
            @else
                <ol class="m-0 list-none divide-y divide-line border-y border-line p-0">
                    @foreach ($booking->emailLogs as $log)
                        <li class="flex flex-col gap-0.5 py-3">
                            <span class="text-base font-medium">{{ $log->typeLabel() }}</span>
                            <span class="text-small text-muted tabular-nums">{{ $log->sent_at->format('d.m.Y H:i') }} Uhr · an <span class="break-all">{{ $log->recipient }}</span></span>
                            @if ($log->subject)<span class="text-small text-muted">„{{ $log->subject }}“</span>@endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-ui.section>

        {{-- Weitere Aktionen --}}
        <section aria-labelledby="weitere-aktionen" x-data="{ open: false }" class="border-t border-line pt-6">
            <h2 id="weitere-aktionen" class="m-0">
                <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-expanded="false"
                        class="focus-inset flex min-h-11 w-full cursor-pointer items-center justify-between gap-4 bg-transparent p-0 text-left text-h2 text-ink">
                    Weitere Aktionen
                    <x-icon.chevron-down class="h-5 w-5 flex-none text-muted transition-transform duration-150" x-bind:class="open && 'rotate-180'" />
                </button>
            </h2>
            <div x-show="open" x-cloak class="mt-6 flex flex-col gap-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)" target="_blank">
                        <x-slot:icon><x-icon.external-link /></x-slot:icon>
                        Kundenansicht öffnen
                    </x-ui.button>
                    @if ($booking->hasTicketDocument() && $isReady)
                        <x-ui.button variant="secondary" :href="route('bookings.ticket', $booking->booking_number)">
                            <x-slot:icon><x-icon.download /></x-slot:icon>
                            Tickets (PDF)
                        </x-ui.button>
                    @endif
                    @if (!$booking->isFree() && !$isExternalInvoicing && $booking->invoice_number)
                        <x-ui.button variant="secondary" :href="route('bookings.invoice', $booking->booking_number)">
                            <x-slot:icon><x-icon.download /></x-slot:icon>
                            Rechnung (PDF)
                        </x-ui.button>
                    @endif
                </div>

                @if ($booking->isActive() && $booking->status !== 'completed')
                    <form action="{{ route('organizer.bookings.cancel', $booking) }}" method="POST" class="flex max-w-form flex-col items-start gap-3 border-t border-line pt-6"
                          onsubmit="return confirm('Buchung wirklich stornieren? Die Plätze werden freigegeben.')">
                        @csrf
                        <h3 class="m-0 text-h3">Buchung stornieren</h3>
                        <x-ui.textarea label="Grund" name="cancellation_reason" optional rows="2" hint="Wird der Person mitgeteilt." class="w-full" />
                        <div>
                            <input type="hidden" name="notify_customer" value="0">
                            <x-ui.checkbox name="notify_customer" value="1" checked label="Stornobestätigung per E-Mail an die Person senden" />
                        </div>
                        <x-ui.button type="submit" variant="danger">Stornieren</x-ui.button>
                    </form>
                @endif

                @unless ($isCancelled)
                    <div class="border-t border-line pt-6">
                        <h3 class="m-0 text-h3">Status manuell setzen</h3>
                        <p class="m-0 mt-2 mb-6 max-w-prose text-small text-muted">
                            „Bezahlt“{{ $isExternalInvoicing ? ' bzw. „Extern fakturiert“' : '' }} bestätigt die Buchung und sendet {{ $deliverable }}.
                            „Erstattet“/„Fehlgeschlagen“ informieren die Person per E-Mail. Übrige Änderungen erfolgen ohne E-Mail.
                        </p>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <form action="{{ route('organizer.bookings.update-status', $booking) }}" method="POST" class="flex items-end gap-3">
                                @csrf @method('PUT')
                                <x-ui.select label="Buchungsstatus" name="status" id="status-select" :old="false" class="min-w-0 flex-1">
                                    @foreach (['pending' => 'Ausstehend', 'pending_approval' => 'Wartet auf Freigabe', 'confirmed' => 'Bestätigt', 'completed' => 'Abgeschlossen'] as $value => $label)
                                        <option value="{{ $value }}" @selected($booking->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </x-ui.select>
                                <x-ui.button type="submit" variant="secondary" size="lg">Setzen</x-ui.button>
                            </form>
                            @unless ($booking->isFree())
                                <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="flex items-end gap-3">
                                    @csrf @method('PUT')
                                    <x-ui.select label="Zahlungsstatus" name="payment_status" id="payment-select" :old="false" class="min-w-0 flex-1">
                                        <option value="pending" @selected($booking->payment_status === 'pending')>Zahlung ausstehend</option>
                                        <option value="paid" @selected($booking->payment_status === 'paid')>Bezahlt</option>
                                        @if ($isExternalInvoicing)
                                            <option value="extern" @selected($booking->payment_status === 'extern')>Extern fakturiert</option>
                                        @endif
                                        <option value="refunded" @selected($booking->payment_status === 'refunded')>Erstattet</option>
                                        <option value="failed" @selected($booking->payment_status === 'failed')>Fehlgeschlagen</option>
                                    </x-ui.select>
                                    <x-ui.button type="submit" variant="secondary" size="lg">Setzen</x-ui.button>
                                </form>
                            @endunless
                        </div>
                    </div>
                @endunless
            </div>
        </section>
    </div>
</x-layouts.app>
