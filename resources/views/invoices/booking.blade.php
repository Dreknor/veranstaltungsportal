<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechnung {{ $invoice_number }}</title>
    <style>
        @include('pdf.partials.styles')
        @include('pdf.partials.invoice-styles')
    </style>
</head>
<body>
    <div class="container">
        @include('pdf.partials.wordmark')
        <table class="invoice-head" style="margin-top: 10px;">
            <tr>
                <td style="width: 60%;">
                    @if($event->organization?->logo)
                        <img src="{{ public_path('storage/' . $event->organization->logo) }}"
                             alt="{{ $event->organization->name }} Logo"
                             style="max-height: 60px; max-width: 150px; margin-bottom: 6px; display: block;">
                    @endif
                    <div class="issuer-info">
                        Veranstaltungsportal | {{ config('app.name') }}<br>
                        {{ $event->organization?->name ?? 'Musterstraße 123, 12345 Musterstadt' }}<br>
                        @if($event->organization?->email)
                            E-Mail: {{ $event->organization->email }} |
                        @endif
                        @if($event->organization?->phone)
                            Tel: {{ $event->organization->phone }}
                        @endif
                    </div>
                </td>
                <td style="width: 40%;">
                    <div class="invoice-label">Rechnung</div>
                </td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td>Rechnungsnr.:</td>
                <td>{{ $invoice_number }}</td>
            </tr>
            <tr>
                <td>Rechnungsdatum:</td>
                <td>{{ $invoice_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td>Buchungsnr.:</td>
                <td>{{ $booking->booking_number }}</td>
            </tr>
            <tr>
                <td>Zahlungsstatus:</td>
                <td>
                    @if($booking->payment_status === 'paid')
                        <span class="status-badge">Bezahlt</span>
                    @else
                        <span class="status-badge status-warning">Ausstehend</span>
                    @endif
                </td>
            </tr>
        </table>

        <table class="parties">
            <tr>
                <td style="width: 55%;">
                    <h3>Rechnungsempfänger</h3>
                    <div>
                        @if($booking->billing_company)
                            <strong>{{ $booking->billing_company }}</strong><br>
                        @endif
                        {{ $booking->customer_name }}<br>
                        {{ $booking->customer_email }}
                        @if($booking->customer_phone)
                            <br>{{ $booking->customer_phone }}
                        @endif
                        @if($booking->billing_vat_id)
                            <br>USt-IdNr.: {{ $booking->billing_vat_id }}
                        @endif
                    </div>
                </td>
                <td style="width: 45%;">
                    <h3>Veranstaltung</h3>
                    <div>
                        <strong>{{ $event->title }}</strong><br>
                        {{ $event->start_date->format('d.m.Y H:i') }} Uhr<br>
                        {{ $event->venue_name }}, {{ $event->venue_city }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%">Beschreibung</th>
                    <th class="text-center" style="width: 15%">Menge</th>
                    <th class="text-right" style="width: 20%">Einzelpreis</th>
                    <th class="text-right" style="width: 15%">Gesamt</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grouped = $items->groupBy('ticket_type_id');
                @endphp

                @foreach($grouped as $ticketTypeId => $groupedItems)
                    @php
                        $firstItem = $groupedItems->first();
                        $quantity = $groupedItems->count();
                        $totalPrice = $groupedItems->sum('price');
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $firstItem->ticketType->name }}</strong>
                            @if($firstItem->ticketType->description)
                                <small>{{ $firstItem->ticketType->description }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $quantity }}</td>
                        <td class="text-right">{{ number_format($firstItem->price, 2, ',', '.') }} €</td>
                        <td class="text-right">{{ number_format($totalPrice, 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Zwischensumme:</td>
                    <td>{{ number_format($booking->subtotal, 2, ',', '.') }} €</td>
                </tr>
                @if($booking->discount > 0)
                    <tr>
                        <td>
                            Rabatt
                            @if($booking->discountCode)
                                ({{ $booking->discountCode->code }})
                            @endif
                        </td>
                        <td>-{{ number_format($booking->discount, 2, ',', '.') }} €</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td>Gesamtbetrag:</td>
                    <td>{{ number_format($booking->total, 2, ',', '.') }} €</td>
                </tr>
            </table>
        </div>

        @if($booking->payment_status !== 'paid')
            <div class="note">
                <h4>Zahlungsinformationen</h4>
                <p>Bitte überweisen Sie den Betrag von <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong> unter Angabe der Buchungsnummer <strong>{{ $booking->booking_number }}</strong> auf folgendes Konto:</p>
                <p style="margin-top: 8px;">
                    <strong>Kontoinhaber:</strong> Veranstaltungsportal<br>
                    <strong>IBAN:</strong> DE89 3704 0044 0532 0130 00<br>
                    <strong>BIC:</strong> COBADEFFXXX<br>
                    <strong>Verwendungszweck:</strong> {{ $booking->booking_number }}
                </p>
            </div>
        @else
            <div class="note note-success">
                <h4>Zahlungsbestätigung</h4>
                <p>Der Betrag von <strong>{{ number_format($booking->total, 2, ',', '.') }} €</strong> wurde erfolgreich bezahlt.</p>
                <p>Vielen Dank für Ihre Zahlung!</p>
            </div>
        @endif

        <div class="block-title">Wichtige Hinweise</div>
        <p class="fine-print">
            Bitte bringen Sie diese Rechnung oder Ihre Buchungsnummer zur Veranstaltung mit.<br>
            Bei Fragen zur Rechnung wenden Sie sich bitte an {{ $event->organization?->email ?? config('mail.from.address') }}<br>
            Dies ist eine maschinell erstellte Rechnung und bedarf keiner Unterschrift.
        </p>

        <div class="footer">
            <p>{{ config('app.name') }} | USt-IdNr.: DE123456789 | Steuernummer: 12/345/67890</p>
        </div>
    </div>
</body>
</html>
