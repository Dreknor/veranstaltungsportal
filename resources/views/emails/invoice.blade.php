<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechnung {{ $invoice->invoice_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0;">
        <h1 style="margin: 0;">Platform-Fee Rechnung</h1>
        <p style="margin: 10px 0 0 0; font-size: 18px;">{{ $invoice->invoice_number }}</p>
    </div>

    <div style="background-color: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; border-top: none;">
        <p>Sehr geehrte/r {{ $invoice->recipient_name }},</p>

        <p>vielen Dank für die Nutzung unserer Event-Plattform. Anbei erhalten Sie die Rechnung für die angefallenen Platform-Fees.</p>

        <h2 style="color: #2563eb; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Rechnungsdetails</h2>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Rechnungsnummer:</td>
                <td style="padding: 8px 0;">{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Rechnungsdatum:</td>
                <td style="padding: 8px 0;">{{ $invoice->invoice_date->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Fälligkeitsdatum:</td>
                <td style="padding: 8px 0;">{{ $invoice->due_date->format('d.m.Y') }}</td>
            </tr>
            @if($invoice->event)
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Veranstaltung:</td>
                <td style="padding: 8px 0;">{{ $invoice->event->title }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; font-weight: bold;">Event-Datum:</td>
                <td style="padding: 8px 0;">{{ $invoice->event->start_date->format('d.m.Y') }}</td>
            </tr>
            @endif
        </table>

        <h2 style="color: #2563eb; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">Rechnungsbetrag</h2>

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr style="background-color: #f3f4f6;">
                <td style="padding: 10px; border: 1px solid #e5e7eb;">Zwischensumme (netto)</td>
                <td style="padding: 10px; border: 1px solid #e5e7eb; text-align: right;">€{{ number_format($invoice->amount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #e5e7eb;">MwSt. ({{ number_format($invoice->tax_rate, 1) }}%)</td>
                <td style="padding: 10px; border: 1px solid #e5e7eb; text-align: right;">€{{ number_format($invoice->tax_amount, 2, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #2563eb; color: white; font-weight: bold; font-size: 18px;">
                <td style="padding: 15px; border: 1px solid #1e40af;">Gesamtbetrag (brutto)</td>
                <td style="padding: 15px; border: 1px solid #1e40af; text-align: right;">€{{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
            </tr>
        </table>

        @if($invoice->status !== 'paid' && isset($invoice->billing_data['platform']))
            @php $platform = $invoice->billing_data['platform']; @endphp
            <div style="background-color: #dbeafe; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #1e40af;">Zahlungsinformationen</h3>
                @if(!empty($platform['bank_name']))
                    <p style="margin: 5px 0;"><strong>Bank:</strong> {{ $platform['bank_name'] }}</p>
                @endif
                @if(!empty($platform['iban']))
                    <p style="margin: 5px 0;"><strong>IBAN:</strong> {{ $platform['iban'] }}</p>
                @endif
                @if(!empty($platform['bic']))
                    <p style="margin: 5px 0;"><strong>BIC:</strong> {{ $platform['bic'] }}</p>
                @endif
                <p style="margin: 5px 0;"><strong>Verwendungszweck:</strong> {{ $invoice->invoice_number }}</p>
                <p style="margin: 15px 0 5px 0;"><strong>Bitte überweisen Sie den Betrag bis zum {{ $invoice->due_date->format('d.m.Y') }}.</strong></p>
            </div>
        @endif

        <p style="margin-top: 30px;">Die detaillierte Rechnung finden Sie im Anhang dieser E-Mail als PDF.</p>

        <p>Sie können Ihre Rechnungen auch jederzeit in Ihrem Dashboard unter <a href="{{ route('organizer.invoices.index') }}" style="color: #2563eb;">Rechnungen</a> einsehen.</p>

        <p style="margin-top: 30px;">Bei Fragen zu dieser Rechnung stehen wir Ihnen gerne zur Verfügung.</p>

        <p>Mit freundlichen Grüßen<br>
        <strong>Ihr Event-Platform Team</strong></p>
    </div>

    <div style="background-color: #f3f4f6; padding: 15px; text-align: center; font-size: 12px; color: #6b7280; border-radius: 0 0 5px 5px;">
        <p style="margin: 5px 0;">Diese E-Mail wurde automatisch generiert.</p>
        <p style="margin: 5px 0;">© {{ date('Y') }} Event Platform - Alle Rechte vorbehalten</p>
    </div>
</body>
</html>

