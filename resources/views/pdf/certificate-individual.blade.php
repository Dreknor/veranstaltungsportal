<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teilnahmezertifikat</title>
    <style>
        @include('pdf.partials.styles')
        @include('pdf.partials.certificate-styles')
    </style>
</head>
<body>
    <div class="certificate-frame">
        <table class="certificate-table">
            <tr>
                <td class="certificate-top">
                    @include('pdf.partials.wordmark')
                    @if($event->organization?->logo)
                        <div style="margin-top: 8px;">
                            <img src="{{ public_path('storage/' . $event->organization->logo) }}"
                                 alt="{{ $event->organization->name }}"
                                 style="max-height: 50px; max-width: 220px;">
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="certificate-main">
                    <div class="certificate-title">Teilnahmezertifikat</div>
                    <div class="certificate-rule"></div>

                    <p class="awarded-to">Hiermit wird bestätigt, dass</p>

                    <div class="attendee-name cert-name">{{ $attendee_name }}</div>

                    <p class="completion-text">erfolgreich an der Veranstaltung</p>

                    <div class="event-title">{{ $event_title }}</div>

                    <p class="event-info">
                        am {{ $event_date->format('d.m.Y') }}
                        @if($duration > 0)
                            ({{ $duration }} Stunden)
                        @endif
                    </p>

                    <p class="completion-text">teilgenommen hat.</p>

                    @if($item->checked_in_at)
                        <p class="event-info" style="font-size: 9pt;">
                            Check-In: {{ $item->checked_in_at->format('d.m.Y H:i') }} Uhr
                        </p>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="certificate-bottom">
                    <table class="signatures">
                        <tr>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-label">
                                    @if($event->organization)
                                        {{ $event->organization->name }}
                                    @else
                                        Veranstalter
                                    @endif
                                </div>
                            </td>
                            <td class="signature-gap"></td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-label">Ort, Datum</div>
                                <div class="signature-value">{{ $issue_date->format('d.m.Y') }}</div>
                            </td>
                        </tr>
                    </table>
                    <table class="certificate-refs">
                        <tr>
                            <td>Ticket: {{ $item->ticket_number }}<br>Buchung: {{ $booking->booking_number }}</td>
                            <td class="num">Zertifikat-Nr.: {{ $certificate_number }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
