<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Teilnahmezertifikat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0;
            margin: 0;
        }

        .certificate-container {
            width: 297mm;
            height: 210mm;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .certificate-border {
            position: absolute;
            top: 15mm;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            border: 4px solid #2563eb;
            border-radius: 8px;
        }

        .certificate-inner-border {
            position: absolute;
            top: 18mm;
            left: 18mm;
            right: 18mm;
            bottom: 18mm;
            border: 1px solid #93c5fd;
        }

        .certificate-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 40mm 30mm;
            text-align: center;
            z-index: 10;
        }

        .certificate-header {
            margin-bottom: 15mm;
        }

        .certificate-logo {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5mm;
        }

        .certificate-title {
            font-size: 48px;
            font-weight: bold;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 3mm;
        }

        .certificate-subtitle {
            font-size: 18px;
            color: #64748b;
            font-style: italic;
        }

        .certificate-body {
            margin: 15mm 0;
        }

        .certificate-text {
            font-size: 16px;
            color: #334155;
            line-height: 1.8;
            margin-bottom: 8mm;
        }

        .attendee-name {
            font-size: 36px;
            font-weight: bold;
            color: #1e293b;
            margin: 8mm 0;
            padding: 5mm 0;
            border-bottom: 2px solid #2563eb;
            display: inline-block;
            min-width: 60%;
        }

        .event-title {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin: 5mm 0;
        }

        .event-details {
            font-size: 14px;
            color: #64748b;
            margin: 3mm 0;
        }

        .certificate-footer {
            margin-top: 15mm;
            display: table;
            width: 100%;
        }

        .signature-block {
            display: table-cell;
            width: 50%;
            padding: 0 20px;
            text-align: center;
        }

        .signature-line {
            width: 80%;
            border-top: 2px solid #94a3b8;
            margin: 5mm auto 2mm;
        }

        .signature-label {
            font-size: 12px;
            color: #64748b;
            font-weight: bold;
        }

        .certificate-meta {
            margin-top: 8mm;
            font-size: 10px;
            color: #94a3b8;
            text-align: center;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(37, 99, 235, 0.03);
            font-weight: bold;
            z-index: 1;
            white-space: nowrap;
        }

        .decorative-element {
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            opacity: 0.1;
        }

        .element-1 {
            top: 20mm;
            left: 20mm;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .element-2 {
            bottom: 20mm;
            right: 20mm;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .seal {
            position: absolute;
            bottom: 25mm;
            right: 35mm;
            width: 60px;
            height: 60px;
            border: 3px solid #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            z-index: 20;
        }

        .seal-text {
            font-size: 8px;
            font-weight: bold;
            color: #2563eb;
            text-align: center;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Decorative Elements -->
        <div class="decorative-element element-1"></div>
        <div class="decorative-element element-2"></div>
        <div class="watermark">BILDUNGSPORTAL</div>

        <!-- Borders -->
        <div class="certificate-border"></div>
        <div class="certificate-inner-border"></div>

        <!-- Official Seal -->
        <div class="seal">
            <div class="seal-text">
                OFFIZIELL<br>
                ZERTIFIZIERT<br>
                {{ date('Y') }}
            </div>
        </div>

        <!-- Content -->
        <div class="certificate-content">
            <div class="certificate-header">
                <div class="certificate-logo">Bildungsportal</div>
                <div class="certificate-title">Teilnahmezertifikat</div>
                <div class="certificate-subtitle">Certificate of Attendance</div>
            </div>

            <div class="certificate-body">
                <div class="certificate-text">
                    Hiermit wird bestätigt, dass
                </div>

                <div class="attendee-name">
                    {{ $attendee_name }}
                </div>

                <div class="certificate-text">
                    erfolgreich an der Fortbildung
                </div>

                <div class="event-title">
                    „{{ $event_title }}"
                </div>

                <div class="event-details">
                    am {{ $event_date->format('d.m.Y') }}
                    @if($duration > 0)
                        ({{ $duration }} Unterrichtsstunden)
                    @endif
                </div>

                <div class="certificate-text" style="margin-top: 5mm;">
                    teilgenommen hat.
                </div>
            </div>

            <div class="certificate-footer">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Ort, Datum</div>
                    <div style="font-size: 11px; margin-top: 2mm;">
                        {{ $event->venue_city }}, {{ $issue_date->format('d.m.Y') }}
                    </div>
                </div>

                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-label">Unterschrift Veranstalter</div>
                    @if($event->organizer_info)
                        <div style="font-size: 11px; margin-top: 2mm;">
                            {{ $event->user->name }}
                        </div>
                    @endif
                </div>
            </div>

            <div class="certificate-meta">
                Zertifikat-Nr.: {{ $certificate_number }}<br>
                Ausgestellt am: {{ $issue_date->format('d.m.Y H:i') }} Uhr<br>
                Veranstaltungs-ID: {{ $event->id }} | Buchungs-Nr.: {{ $booking->booking_number }}
            </div>
        </div>
    </div>
</body>
</html>

