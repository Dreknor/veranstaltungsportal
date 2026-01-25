<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teilnahmezertifikat</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 297mm;
            height: 210mm;
            position: relative;
            color: #333;
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            padding: 40px;
            position: relative;
        }

        .certificate-border {
            border: 8px solid #fff;
            width: 100%;
            height: 100%;
            padding: 30px;
            position: relative;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
        }

        .certificate-content {
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .header {
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 48px;
            color: #667eea;
            letter-spacing: 3px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 16px;
            color: #666;
            letter-spacing: 1px;
        }

        .awarded-to {
            margin: 40px 0 20px;
            font-size: 18px;
            color: #666;
            font-style: italic;
        }

        .attendee-name {
            font-size: 56px;
            color: #333;
            font-weight: bold;
            margin: 20px 0;
            padding: 20px;
            border-bottom: 3px solid #667eea;
            display: inline-block;
            min-width: 500px;
        }

        .event-details {
            margin: 40px 0;
        }

        .completion-text {
            font-size: 18px;
            color: #666;
            line-height: 1.8;
            margin: 15px 0;
        }

        .event-title {
            font-size: 28px;
            color: #667eea;
            font-weight: bold;
            margin: 15px 0;
        }

        .event-info {
            font-size: 16px;
            color: #666;
            margin: 10px 0;
        }

        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 0 50px;
        }

        .signature-block {
            text-align: center;
            flex: 1;
        }

        .signature-line {
            border-top: 2px solid #333;
            width: 200px;
            margin: 40px auto 10px;
        }

        .signature-label {
            font-size: 12px;
            color: #666;
        }

        .certificate-number {
            position: absolute;
            bottom: 20px;
            right: 40px;
            font-size: 11px;
            color: #999;
        }

        .ticket-info {
            position: absolute;
            bottom: 20px;
            left: 40px;
            font-size: 10px;
            color: #999;
        }

        .decorative-line {
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="certificate-content">
                <!-- Header -->
                <div class="header">
                    @if($event->organization?->logo)
                        <div style="margin-bottom: 20px;">
                            <img src="{{ public_path('storage/' . $event->organization->logo) }}"
                                 alt="{{ $event->organization->name }}"
                                 style="max-height: 60px; max-width: 250px; object-fit: contain;">
                        </div>
                    @endif
                    <h1>Teilnahmezertifikat</h1>
                    <p>Certificate of Attendance</p>
                </div>

                <div class="decorative-line"></div>

                <!-- Awarded To -->
                <p class="awarded-to">Hiermit wird bestätigt, dass</p>

                <!-- Attendee Name -->
                <div class="attendee-name">{{ $attendee_name }}</div>

                <!-- Event Details -->
                <div class="event-details">
                    <p class="completion-text">
                        erfolgreich an der Veranstaltung
                    </p>

                    <div class="event-title">{{ $event_title }}</div>

                    <p class="event-info">
                        am {{ $event_date->format('d.m.Y') }}
                        @if($duration > 0)
                            ({{ $duration }} Stunden)
                        @endif
                    </p>

                    <p class="completion-text" style="margin-top: 20px;">
                        teilgenommen hat.
                    </p>

                    @if($item->checked_in_at)
                        <p class="event-info" style="font-size: 14px; color: #999; margin-top: 10px;">
                            Check-In: {{ $item->checked_in_at->format('d.m.Y H:i') }} Uhr
                        </p>
                    @endif
                </div>

                <div class="decorative-line"></div>

                <!-- Footer -->
                <div class="footer">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <p class="signature-label">
                            @if($event->organization)
                                {{ $event->organization->name }}
                            @else
                                Veranstalter
                            @endif
                        </p>
                    </div>

                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <p class="signature-label">Ort, Datum</p>
                        <p class="signature-label" style="margin-top: 5px;">{{ $issue_date->format('d.m.Y') }}</p>
                    </div>
                </div>

                <!-- Certificate Number -->
                <div class="certificate-number">
                    Zertifikat-Nr.: {{ $certificate_number }}
                </div>

                <!-- Ticket Info -->
                <div class="ticket-info">
                    Ticket: {{ $item->ticket_number }}<br>
                    Buchung: {{ $booking->booking_number }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
