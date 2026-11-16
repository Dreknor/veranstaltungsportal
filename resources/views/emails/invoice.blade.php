@extends('emails.layout')

@php
    $label =
        'padding: 8px 16px 8px 0; width: 170px; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 14px; line-height: 1.5; color: #55676A;';
    $value =
        'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135; font-variant-numeric: tabular-nums;';
    $amount =
        'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135; text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums;';
    $h2 = 'margin: 32px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;';
@endphp

@section('title')
    Rechnung {{ $invoice->invoice_number }}
@endsection

@section('heading', 'Plattformgebühren-Rechnung')

@section('subheading')
    {{ $invoice->invoice_number }}
@endsection

@section('content')
    <p style="margin: 0 0 16px 0;">Sehr geehrte/r {{ $invoice->recipient_name }},</p>

    <p style="margin: 0;">vielen Dank für die Nutzung unserer Veranstaltungsplattform. Anbei erhalten Sie die Rechnung für
        die angefallenen Plattformgebühren.</p>

    <h2 style="{{ $h2 }}">Rechnungsdetails</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-top: 1px solid #DCE3E2;">
        <tr>
            <td style="{{ $label }}">Rechnungsnummer</td>
            <td style="{{ $value }}">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">Rechnungsdatum</td>
            <td style="{{ $value }}">{{ $invoice->invoice_date->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="{{ $label }}">Fälligkeitsdatum</td>
            <td style="{{ $value }}">{{ $invoice->due_date->format('d.m.Y') }}</td>
        </tr>
        @if ($invoice->event)
            <tr>
                <td style="{{ $label }}">Veranstaltung</td>
                <td style="{{ $value }}">{{ $invoice->event->title }}</td>
            </tr>
            <tr>
                <td style="{{ $label }}">Veranstaltungsdatum</td>
                <td style="{{ $value }}">{{ $invoice->event->start_date->format('d.m.Y') }}</td>
            </tr>
        @endif
    </table>

    <h2 style="{{ $h2 }}">Rechnungsbetrag</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-top: 1px solid #DCE3E2;">
        <tr>
            <td style="{{ $value }}">Zwischensumme (netto)</td>
            <td style="{{ $amount }}">{{ number_format($invoice->amount, 2, ',', '.') }} €</td>
        </tr>
        <tr>
            <td style="{{ $value }}">MwSt. ({{ number_format($invoice->tax_rate, 1) }}%)</td>
            <td style="{{ $amount }}">{{ number_format($invoice->tax_amount, 2, ',', '.') }} €</td>
        </tr>
        <tr>
            <td style="{{ $value }} font-size: 16px; font-weight: 600;">Gesamtbetrag (brutto)</td>
            <td style="{{ $amount }} font-size: 16px; font-weight: 600;">
                {{ number_format($invoice->total_amount, 2, ',', '.') }} €</td>
        </tr>
    </table>

    @if ($invoice->status !== 'paid' && isset($invoice->billing_data['platform']))
        @php $platform = $invoice->billing_data['platform']; @endphp
        @component('emails.partials.notice', [
            'type' => 'info',
            'title' => 'Zahlungsinformationen',
            'margin' => '32px 0 24px 0',
        ])
            @if (!empty($platform['bank_name']))
                <p style="margin: 0 0 4px 0;"><strong>Bank:</strong> {{ $platform['bank_name'] }}</p>
            @endif
            @if (!empty($platform['iban']))
                <p style="margin: 0 0 4px 0;"><strong>IBAN:</strong> {{ $platform['iban'] }}</p>
            @endif
            @if (!empty($platform['bic']))
                <p style="margin: 0 0 4px 0;"><strong>BIC:</strong> {{ $platform['bic'] }}</p>
            @endif
            <p style="margin: 0 0 4px 0;"><strong>Verwendungszweck:</strong> {{ $invoice->invoice_number }}</p>
            <p style="margin: 12px 0 0 0;"><strong>Bitte überweisen Sie den Betrag bis zum
                    {{ $invoice->due_date->format('d.m.Y') }}.</strong></p>
        @endcomponent
    @endif

    <p style="margin: 24px 0 16px 0;">Die detaillierte Rechnung finden Sie im Anhang dieser E-Mail als PDF.</p>

    <p style="margin: 0 0 16px 0;">Sie können Ihre Rechnungen auch jederzeit in Ihrem Dashboard unter <a
            href="{{ route('organizer.invoices.index') }}"
            style="color: #1E3135; text-decoration: underline;">Rechnungen</a> einsehen.</p>

    <p style="margin: 0 0 16px 0;">Bei Fragen zu dieser Rechnung stehen wir Ihnen gerne zur Verfügung.</p>

    <p style="margin: 0;">Mit freundlichen Grüßen<br>
        Ihr {{ config('app.name') }}-Team</p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert.')
