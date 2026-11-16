<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Rechnung - {{ $invoice->invoice_number }}</title>
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
                    <div class="issuer-info">
                        @if(isset($invoice->billing_data['platform']))
                            @php $platform = $invoice->billing_data['platform']; @endphp
                            <strong>{{ $platform['company_name'] ?? config('app.name') }}</strong><br>
                            {{ $platform['address'] ?? '' }}<br>
                            {{ $platform['postal_code'] ?? '' }} {{ $platform['city'] ?? '' }}<br>
                            @if(!empty($platform['tax_id']))
                                Steuer-Nr: {{ $platform['tax_id'] }}<br>
                            @endif
                            @if(!empty($platform['vat_id']))
                                USt-IdNr: {{ $platform['vat_id'] }}<br>
                            @endif
                            E-Mail: {{ $platform['email'] ?? '' }}
                        @endif
                    </div>
                </td>
                <td style="width: 40%;">
                    <div class="invoice-label">Rechnung</div>
                    <div class="invoice-meta">Platform-Fee Abrechnung</div>
                </td>
            </tr>
        </table>

        <table class="parties">
            <tr>
                <td style="width: 55%;">
                    <h3>Rechnungsempfänger</h3>
                    <div>
                        <strong>{{ $invoice->recipient_name }}</strong>
                        @if($invoice->recipient_address)
                            <br>{!! nl2br(e($invoice->recipient_address)) !!}
                        @endif
                    </div>
                </td>
                <td style="width: 45%;"></td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td>Rechnungsnummer:</td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td>Rechnungsdatum:</td>
                <td>{{ $invoice->invoice_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td>Fälligkeitsdatum:</td>
                <td>{{ $invoice->due_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td>Veranstaltung:</td>
                <td>{{ $invoice->event->title ?? 'N/A' }}</td>
            </tr>
            @if($invoice->event)
            <tr>
                <td>Veranstaltungsdatum:</td>
                <td>{{ $invoice->event->start_date->format('d.m.Y') }}</td>
            </tr>
            @endif
        </table>

        <div class="block-title">Leistungsbeschreibung</div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Beschreibung</th>
                    <th class="text-right">Menge</th>
                    <th class="text-right">Einzelpreis</th>
                    <th class="text-right">Gesamt</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($invoice->billing_data['items']))
                    @foreach($invoice->billing_data['items'] as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            {{ $item['description'] ?? 'Platform-Fee' }}
                            <small>{{ $item['details'] ?? '' }}</small>
                        </td>
                        <td class="text-right">{{ $item['quantity'] ?? 1 }}</td>
                        <td class="text-right">{{ number_format($item['unit_price'] ?? $item['total'], 2, ',', '.') }} €</td>
                        <td class="text-right">{{ number_format($item['total'], 2, ',', '.') }} €</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td>1</td>
                        <td>Platform-Fee für Veranstaltung</td>
                        <td class="text-right">1</td>
                        <td class="text-right">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
                        <td class="text-right">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="totals">
            <table>
                @if(isset($invoice->billing_data['breakdown']))
                    @php $breakdown = $invoice->billing_data['breakdown']; @endphp
                    @if($breakdown['booking_fees'] > 0)
                    <tr>
                        <td>Buchungsgebühren (netto):</td>
                        <td>{{ number_format($breakdown['booking_fees'], 2, ',', '.') }} €</td>
                    </tr>
                    @endif
                    @if($breakdown['featured_fees'] > 0)
                    <tr>
                        <td>Gebühren für hervorgehobene Veranstaltungen (netto):</td>
                        <td>{{ number_format($breakdown['featured_fees'], 2, ',', '.') }} €</td>
                    </tr>
                    @endif
                @endif
                <tr>
                    <td>Zwischensumme (netto):</td>
                    <td>{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td>MwSt. ({{ number_format($invoice->tax_rate, 1) }}%):</td>
                    <td>{{ number_format($invoice->tax_amount, 2, ',', '.') }} €</td>
                </tr>
                <tr class="total-row">
                    <td>Gesamtbetrag (brutto):</td>
                    <td>{{ number_format($invoice->total_amount, 2, ',', '.') }} €</td>
                </tr>
            </table>
        </div>

        @if($invoice->status !== 'paid')
        <div class="note">
            <h4>Zahlungsinformationen</h4>
            Bitte überweisen Sie den Betrag bis zum <strong>{{ $invoice->due_date->format('d.m.Y') }}</strong>.<br>
            @if(isset($invoice->billing_data['platform']))
                @php $platform = $invoice->billing_data['platform']; @endphp
                @if(!empty($platform['bank_name']))
                    <br>
                    <strong>Bankverbindung:</strong><br>
                    Bank: {{ $platform['bank_name'] }}<br>
                    @if(!empty($platform['iban']))
                        IBAN: {{ $platform['iban'] }}<br>
                    @endif
                    @if(!empty($platform['bic']))
                        BIC: {{ $platform['bic'] }}<br>
                    @endif
                @endif
            @endif
            Verwendungszweck: {{ $invoice->invoice_number }}
        </div>
        @else
        <div class="note note-success">
            <h4>Zahlung erhalten</h4>
            Diese Rechnung wurde bereits am {{ $invoice->paid_at->format('d.m.Y') }} bezahlt. Vielen Dank!
        </div>
        @endif

        <div class="block-title">Hinweise</div>
        <p class="fine-print">
            Diese Rechnung wird gemäß § 14 UStG für erbrachte Leistungen ausgestellt.<br>
            Die Platform-Fee wird für die Bereitstellung der Veranstaltungsplattform und Zahlungsabwicklung berechnet.<br>
            Zahlungsziel: {{ $invoice->due_date->format('d.m.Y') }}<br>
            Diese Rechnung entspricht der europäischen Norm EN 16931 (ZUGFeRD) für die elektronische Rechnungsstellung.
        </p>

        <div class="footer">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; text-align: left; vertical-align: top;">
                        @if(isset($invoice->billing_data['platform']))
                            @php $platform = $invoice->billing_data['platform']; @endphp
                            <strong>Kontakt:</strong><br>
                            E-Mail: {{ $platform['email'] ?? 'support@platform.local' }}<br>
                            @if(!empty($platform['phone']))
                                Tel: {{ $platform['phone'] }}
                            @endif
                        @endif
                    </td>
                    <td style="width: 50%; text-align: right; vertical-align: top;">
                        Vielen Dank für Ihre Zusammenarbeit!
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
