<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px 20px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #667eea;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        .event-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .event-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        .event-meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .event-meta i {
            margin-right: 5px;
        }
        .event-description {
            font-size: 14px;
            color: #555;
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #5568d3;
        }
        .footer {
            background-color: #f0f0f0;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>📚 {{ config('app.name') }}</h1>
            <p>Ihr {{ $type === 'weekly' ? 'wöchentlicher' : 'monatlicher' }} Newsletter</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                Hallo {{ $subscriber->fullName() }},
            </div>

            <p>
                wir haben wieder spannende Bildungsangebote für Sie zusammengestellt!
            </p>

            <!-- Featured Events -->
            @if($featuredEvents->count() > 0)
            <div class="section">
                <div class="section-title">⭐ Highlights der Woche</div>
                @foreach($featuredEvents as $event)
                <div class="event-card">
                    <div class="event-title">{{ $event->title }}</div>
                    <div class="event-meta">
                        📅 {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                    </div>
                    <div class="event-meta">
                        📍 {{ $event->isOnline() ? 'Online-Veranstaltung' : $event->venue_name }}
                    </div>
                    @if($event->category)
                    <div class="event-meta">
                        🏷️ {{ $event->category->name }}
                    </div>
                    @endif
                    <div class="event-description">
                        {{ Str::limit(strip_tags($event->description), 120) }}
                    </div>
                    <a href="{{ route('events.show', $event) }}" class="btn">Jetzt anmelden</a>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Personalized Recommendations -->
            @if($recommendations->count() > 0)
            <div class="section">
                <div class="section-title">💡 Passend zu Ihren Interessen</div>
                @foreach($recommendations as $event)
                <div class="event-card">
                    <div class="event-title">{{ $event->title }}</div>
                    <div class="event-meta">
                        📅 {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                    </div>
                    <div class="event-meta">
                        📍 {{ $event->isOnline() ? 'Online-Veranstaltung' : $event->venue_name }}
                    </div>
                    @if($event->category)
                    <div class="event-meta">
                        🏷️ {{ $event->category->name }}
                    </div>
                    @endif
                    <a href="{{ route('events.show', $event) }}" class="btn">Mehr erfahren</a>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Upcoming Events -->
            @if($upcomingEvents->count() > 0)
            <div class="section">
                <div class="section-title">📅 Kommende Veranstaltungen</div>
                @foreach($upcomingEvents->take(5) as $event)
                <div class="event-card">
                    <div class="event-title">{{ $event->title }}</div>
                    <div class="event-meta">
                        📅 {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                    </div>
                    <div class="event-meta">
                        📍 {{ $event->isOnline() ? 'Online-Veranstaltung' : $event->venue_name }}
                    </div>
                    @if($event->category)
                    <div class="event-meta">
                        🏷️ {{ $event->category->name }}
                    </div>
                    @endif
                    <a href="{{ route('events.show', $event) }}" class="btn">Details ansehen</a>
                </div>
                @endforeach
            </div>
            @endif

            <p style="margin-top: 30px;">
                Viel Freude beim Entdecken und Lernen!<br>
                Ihr {{ config('app.name') }} Team
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>
                Sie erhalten diese E-Mail, weil Sie unseren Newsletter abonniert haben.<br>
                <a href="{{ route('settings.interests.edit') }}">Interessen verwalten</a> |
                <a href="{{ route('newsletter.unsubscribe') }}">Abmelden</a>
            </p>
            <p>
                © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.
            </p>
        </div>
    </div>
</body>
</html>

