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

        .seal {
            position: absolute;
            bottom: 80px;
            left: 60px;
            width: 100px;
            height: 100px;
            border: 4px solid #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background: rgba(102, 126, 234, 0.1);
        }

        .seal-text {
            font-size: 10px;
            color: #667eea;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }

        .seal-year {
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
            margin-top: 5px;
        }

        .ornament {
            text-align: center;
            margin: 20px 0;
        }

        .ornament-line {
            display: inline-block;
            width: 60px;
            height: 3px;
            background: #667eea;
            margin: 0 15px;
            vertical-align: middle;
        }

        .ornament-icon {
            display: inline-block;
            font-size: 24px;
            color: #667eea;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-border">
            <div class="certificate-content">
                <div class="header">
                    <h1>Teilnahmezertifikat</h1>
                    <p>Certificate of Attendance</p>
                </div>

                <div class="ornament">
                    <span class="ornament-line"></span>
                    <span class="ornament-icon">★</span>
                    <span class="ornament-line"></span>
                </div>

                <div class="awarded-to">
                    Hiermit wird bestätigt, dass
                </div>

                <div class="attendee-name">
                    {{ $attendee_name }}
                </div>

                <div class="event-details">
                    <p class="completion-text">
                        erfolgreich an der folgenden Fortbildungsveranstaltung teilgenommen hat:
                    </p>

                    <div class="event-title">
                        {{ $event_title }}
                    </div>

                    <div class="event-info">
                        <strong>Datum:</strong> {{ $event_date->format('d. F Y') }}
                        <br>
                        <strong>Dauer:</strong> {{ $duration }}
                        @if($event->venue_city)
                            <br>
                            <strong>Ort:</strong> {{ $event->venue_city }}
                        @endif
                    </div>

                    <div class="ornament">
                        <span class="ornament-line"></span>
                        <span class="ornament-icon">★</span>
                        <span class="ornament-line"></span>
                    </div>
                </div>

                <div class="footer">
                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-label">Datum</div>
                        <div style="margin-top: 5px; font-size: 14px;">
                            {{ $issue_date->format('d.m.Y') }}
                        </div>
                    </div>

                    <div class="signature-block">
                        <div class="signature-line"></div>
                        <div class="signature-label">Veranstalter</div>
                        <div style="margin-top: 5px; font-size: 14px;">
                            {{ $event->user->organization_name ?? $event->user->fullName() }}
                        </div>
                    </div>
                </div>

                <div class="seal">
                    <div class="seal-text">BESTÄTIGT</div>
                    <div class="seal-year">{{ $event_date->format('Y') }}</div>
                </div>

                <div class="certificate-number">
                    Zertifikatsnummer: {{ $certificate_number }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

