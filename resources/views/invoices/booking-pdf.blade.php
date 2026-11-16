<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rechnung - {{ $booking->invoice_number ?? $booking->booking_number }}</title>
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
                @if(!empty($organizer['logo_path']))
                    <img src="{{ $organizer['logo_path'] }}" alt="{{ $organizer['company_name'] ?? 'Logo' }}" style="max-height: 60px; max-width: 200px; margin-bottom: 8px; display: block;">
                @else
                    <div class="issuer-name">{{ $organizer['company_name'] ?? 'Rechnung' }}</div>
                @endif
                <div class="issuer-info">
                    <strong>{{ $organizer['company_name'] ?? 'Veranstalter' }}</strong><br>
                    {{ $organizer['address'] ?? '' }}<br>
                    {{ $organizer['postal_code'] ?? '' }} {{ $organizer['city'] ?? '' }}<br>
                    @if(!empty($organizer['tax_id']))
                        Steuer-Nr: {{ $organizer['tax_id'] }}<br>
                    @endif
                    @if(!empty($organizer['email']))
                        E-Mail: {{ $organizer['email'] }}
                    @endif
                </div>
            </td>
            <td style="width: 40%;">
                <div class="invoice-label">Rechnung</div>
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td style="width: 55%;">
                <h3>Rechnungsempfänger</h3>
                <div>
                    @if(!empty($customer['company']))
                        <strong>{{ $customer['company'] }}</strong><br>
                    @endif
                    <strong>{{ $customer['name'] }}</strong><br>
                    {{ $customer['address'] ?? '' }}
                    @if(!empty($customer['vat_id']))
                        <br>USt-IdNr.: {{ $customer['vat_id'] }}
                    @endif
                </div>
            </td>
            <td style="width: 45%;"></td>
        </tr>
    </table>

    <table class="meta-table">
        <tr>
            <td>Rechnungsnummer:</td>
            <td>{{ $booking->invoice_number }}</td>
        </tr>
        <tr>
            <td>Rechnungsdatum:</td>
            <td>{{ optional($booking->invoice_date)->format('d.m.Y') ?? now()->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td>Buchungsnummer:</td>
            <td>{{ $booking->booking_number }}</td>
        </tr>
        <tr>
            <td>Veranstaltung:</td>
            <td>{{ $event->title }}</td>
        </tr>
        <tr>
            <td>Datum:</td>
            <td>{{ $event->start_date->format('d.m.Y H:i') }} Uhr</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Position</th>
                <th class="text-right">Anzahl</th>
                <th class="text-right">Einzelpreis</th>
                <th class="text-right">Gesamt</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['description'] }}</td>
                <td class="text-right">{{ $item['quantity'] }}</td>
                <td class="text-right">{{ number_format($item['unit_price'], 2, ',', '.') }} €</td>
                <td class="text-right">{{ number_format($item['total'], 2, ',', '.') }} €</td>
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
                <td>Rabatt:</td>
                <td>-{{ number_format($booking->discount, 2, ',', '.') }} €</td>
            </tr>
            @endif
            <tr>
                <td>MwSt. (19%):</td>
                <td>{{ number_format($booking->total * 0.19 / 1.19, 2, ',', '.') }} €</td>
            </tr>
            <tr class="total-row">
                <td>Gesamtbetrag:</td>
                <td>{{ number_format($booking->total, 2, ',', '.') }} €</td>
            </tr>
        </table>
    </div>

    @if($booking->payment_status !== 'paid')
    <div class="note">
        <h4>Zahlungshinweise</h4>
        Bitte überweisen Sie den Betrag bis zum {{ $event->start_date->subDays(7)->format('d.m.Y') }}.<br>
        <br>
        <strong>Bankverbindung:</strong><br>
        @if(!empty($organizer['bank_account']))
            @php $bankAccount = $organizer['bank_account']; @endphp
            Kontoinhaber: {{ $bankAccount['account_holder'] ?? $organizer['company_name'] }}<br>
            @if(!empty($bankAccount['bank_name']))
                Bank: {{ $bankAccount['bank_name'] }}<br>
            @endif
            IBAN: {{ $bankAccount['iban'] ?? 'Wird noch bekannt gegeben' }}<br>
            @if(!empty($bankAccount['bic']))
                BIC: {{ $bankAccount['bic'] }}<br>
            @endif
        @else
            Bankverbindung: Wird noch bekannt gegeben<br>
        @endif
        <br>
        Verwendungszweck: {{ $booking->invoice_number ?? $booking->booking_number }}
    </div>
    @else
    <div class="note note-success">
        <h4>Zahlung erhalten</h4>
        Diese Rechnung wurde bereits bezahlt. Vielen Dank!
    </div>
    @endif

    <div class="footer">
        Vielen Dank für Ihre Buchung!<br>
        Bei Fragen wenden Sie sich bitte an: {{ $organizer['email']  }}
    </div>
</div>
</body>
</html>

