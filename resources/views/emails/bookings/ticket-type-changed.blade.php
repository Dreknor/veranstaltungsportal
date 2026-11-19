@extends('emails.layout')

@php
    $contactEmail = $booking->event->getOrganizerEmail() ?? config('mail.from.address');
    $row =
        'padding: 8px 0; vertical-align: top; border-bottom: 1px solid #DCE3E2; font-size: 15px; line-height: 1.5; color: #1E3135;';
    $num = $row . ' text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums;';
    $h2 = 'margin: 8px 0 8px 0; font-size: 18px; line-height: 1.3; font-weight: 600; color: #1E3135;';
    $link = 'color: #1E3135; text-decoration: underline;';
    $showPrices = !$booking->isFree() || $totalChanged;
    $money = fn($amount) => number_format((float) $amount, 2, ',', '.') . ' €';
@endphp

@section('title', 'Ticketart geändert')

@if ($booking->event->organization?->logo)
    @section('header_logo')
        <img src="{{ asset('storage/' . $booking->event->organization->logo) }}"
            alt="{{ $booking->event->organization->name }} Logo"
            style="display: inline-block; max-height: 40px; max-width: 140px; height: auto; border: 0;">
    @endsection
@endif

@section('heading', 'Ticketart geändert')

@section('content')
    <p style="margin: 0 0 16px 0;">Hallo {{ $booking->customer_name }},</p>

    <p style="margin: 0 0 24px 0;">
        der Veranstalter hat in Ihrer Buchung <strong>{{ $booking->booking_number }}</strong> für
        <strong>{{ $booking->event->title }}</strong> die Ticketart geändert.
    </p>

    @component('emails.partials.notice', ['type' => 'info'])
        <p style="margin: 0;"><strong>Begründung des Veranstalters:</strong> {{ $reason }}</p>
    @endcomponent

    <h2 style="{{ $h2 }}">Änderungen</h2>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-top: 1px solid #DCE3E2; margin: 0 0 24px 0;">
        @foreach ($changes as $change)
            <tr>
                <td style="{{ $row }}">
                    <strong>{{ $change['attendee'] }}</strong><br>
                    <span style="color: #55676A;">{{ $change['old_ticket_type'] }} →
                    </span>{{ $change['new_ticket_type'] }}
                </td>
                @if ($showPrices)
                    <td style="{{ $num }}">
                        @if ((float) $change['old_price'] !== (float) $change['new_price'])
                            <span
                                style="color: #55676A; text-decoration: line-through;">{{ $money($change['old_price']) }}</span><br>
                        @endif
                        {{ $money($change['new_price']) }}
                    </td>
                @endif
            </tr>
        @endforeach
        @if ($totalChanged)
            <tr>
                <td style="{{ $row }} font-weight: 600;">Neuer Gesamtbetrag</td>
                <td style="{{ $num }} font-weight: 600;">{{ $money($booking->total) }}</td>
            </tr>
        @endif
    </table>

    @if ($priceLocked)
        <p style="margin: 0 0 16px 0;">Der Preis Ihrer bereits bezahlten Buchung bleibt unverändert. Bei Fragen zur
            Abrechnung wenden Sie sich bitte direkt an den Veranstalter.</p>
    @elseif ($totalChanged && $booking->isFree())
        <p style="margin: 0 0 16px 0;">Für Ihre Buchung ist keine Zahlung mehr nötig.</p>
    @elseif ($totalChanged && $booking->payment_status === 'extern')
        <p style="margin: 0 0 16px 0;">Die Abrechnung erfolgt weiterhin direkt über den Veranstalter.</p>
    @elseif ($totalChanged && (float) $oldTotal == 0.0)
        <p style="margin: 0 0 16px 0;">Ihre Buchung ist damit kostenpflichtig. Die Rechnung und die Zahlungshinweise
            erhalten Sie in einer separaten E-Mail.</p>
    @elseif ($totalChanged && $booking->payment_status === 'pending')
        <p style="margin: 0 0 16px 0;">Bitte zahlen Sie den neuen Gesamtbetrag von
            <strong>{{ $money($booking->total) }}</strong>{{ $invoiceAttached ? '. Die korrigierte Rechnung finden Sie im Anhang' : '' }}.
        </p>
    @endif

    @if ($booking->hasTicketDocument() && $booking->canSendTickets())
        <p style="margin: 0 0 16px 0;">Im Anhang finden Sie Ihre neu ausgestellten Tickets. Ticketnummern und QR-Codes
            bleiben gültig, bitte verwenden Sie dennoch die neuen Tickets.</p>
    @endif

    @include('emails.partials.button', [
        'url' => $booking->manageUrl(),
        'label' => 'Buchungsdetails ansehen',
        'margin' => '8px 0 24px 0',
    ])

    <p style="margin: 0 0 16px 0;">Bei Fragen wenden Sie sich bitte an <a href="mailto:{{ $contactEmail }}"
            style="{{ $link }}">{{ $contactEmail }}</a>.</p>

    <p style="margin: 0;">
        Viele Grüße<br>
        Ihr {{ config('app.name') }}-Team
    </p>
@endsection

@section('footer_note', 'Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.')
