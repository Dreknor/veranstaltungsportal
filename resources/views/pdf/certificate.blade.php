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
                </td>
            </tr>
            <tr>
                <td class="certificate-main">
                    <div class="certificate-title">Teilnahmezertifikat</div>
                    <div class="certificate-rule"></div>

                    <p class="awarded-to">Hiermit wird bestätigt, dass</p>

                    <div class="attendee-name cert-name">{{ $attendee_name }}</div>

                    <p class="completion-text">
                        erfolgreich an der folgenden Fortbildungsveranstaltung teilgenommen hat:
                    </p>

                    <div class="event-title">{{ $event_title }}</div>

                    <div class="event-info">
                        <strong>Datum:</strong> {{ $event_date->format('d. F Y') }}
                        <br>
                        <strong>Dauer:</strong> {{ $duration }}
                        @if($event->venue_city)
                            <br>
                            <strong>Ort:</strong> {{ $event->venue_city }}
                        @endif
                    </div>
                </td>
            </tr>
            <tr>
                <td class="certificate-bottom">
                    <table class="signatures">
                        <tr>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-label">Datum</div>
                                <div class="signature-value">{{ $issue_date->format('d.m.Y') }}</div>
                            </td>
                            <td class="signature-gap"></td>
                            <td>
                                <div class="signature-line"></div>
                                <div class="signature-label">Veranstalter</div>
                                <div class="signature-value">{{ $event->user->organization_name ?? $event->user->fullName() }}</div>
                            </td>
                        </tr>
                    </table>
                    <div class="certificate-number">Zertifikatsnummer: {{ $certificate_number }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
