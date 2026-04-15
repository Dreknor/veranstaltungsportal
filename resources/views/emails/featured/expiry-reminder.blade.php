<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hervorhebungs-Status läuft ab</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .event-details {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .event-details h3 {
            margin-top: 0;
            color: #667eea;
        }
        .detail-row {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            width: 140px;
            color: #6b7280;
        }
        .detail-value {
            flex: 1;
            color: #111827;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            font-weight: 600;
        }
        .button-secondary {
            background: #10b981;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
            border-top: 1px solid #e5e7eb;
            margin-top: 30px;
        }
        .highlight {
            color: #f59e0b;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>⏰ Hervorhebungs-Status läuft bald ab</h1>
    </div>

    <div class="content">
        <p>Hallo {{ $user->name }},</p>

        <div class="warning-box">
            <strong>⚠️ Wichtiger Hinweis:</strong> Der Hervorhebungs-Status Ihrer Veranstaltung läuft in <span class="highlight">3 Tagen</span> ab!
        </div>

        <p>Ihre Veranstaltung wird aktuell als „Empfohlen" prominent auf der Startseite angezeigt. Diese Hervorhebung endet am <strong>{{ $fee->featured_end_date->format('d.m.Y') }}</strong>.</p>

        <div class="event-details">
            <h3>📅 Veranstaltungsdetails</h3>
            <div class="detail-row">
                <div class="detail-label">Veranstaltung:</div>
                <div class="detail-value">{{ $event->title }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Hervorgehoben seit:</div>
                <div class="detail-value">{{ $fee->featured_start_date->format('d.m.Y') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Hervorgehoben bis:</div>
                <div class="detail-value">{{ $fee->featured_end_date->format('d.m.Y') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Verbleibende Tage:</div>
                <div class="detail-value" style="color: #f59e0b; font-weight: bold;">3 Tage</div>
            </div>
        </div>

        <h3>💡 Möchten Sie die Hervorhebung verlängern?</h3>
        <p>Um die Sichtbarkeit Ihrer Veranstaltung weiterhin zu maximieren, können Sie den Hervorhebungs-Status jetzt verlängern:</p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('organizer.featured-events.extend', $event) }}" class="button button-secondary">
                ✨ Jetzt verlängern
            </a>
            <a href="{{ route('organizer.featured-events.history') }}" class="button">
                📊 Hervorhebungs-Historie ansehen
            </a>
        </div>

        <h3>❓ Was passiert nach Ablauf?</h3>
        <p>Nach dem Ablaufdatum wird Ihre Veranstaltung:</p>
        <ul>
            <li>✓ Weiterhin veröffentlicht und buchbar bleiben</li>
            <li>✓ In den normalen Suchergebnissen erscheinen</li>
            <li>✗ Nicht mehr prominent auf der Startseite angezeigt werden</li>
            <li>✗ Kein „Empfohlen"-Badge mehr haben</li>
        </ul>

        <p style="margin-top: 30px;">Bei Fragen stehen wir Ihnen gerne zur Verfügung!</p>

        <p>
            Mit freundlichen Grüßen<br>
            <strong>Ihr {{ config('app.name')  }}-Team</strong>
        </p>
    </div>

    <div class="footer">
        <p>
            Diese E-Mail wurde automatisch versendet.<br>
            Sie können Benachrichtigungen in Ihren <a href="{{ route('settings.notifications.edit') }}">Einstellungen</a> verwalten.
        </p>
        <p>© {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.</p>
    </div>
</body>
</html>
