<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veranstaltungserinnerung</title>
</head>
@php
    $session = $session ?? null;
    $start = $session?->start_date ?? $event->start_date;
    $end = $session?->end_date ?? ($session ? null : $event->end_date);
    $venueSource = $session ?? $event;
    $startsIn = now()->diff($start);
    $startsInText = $start->isPast()
        ? 'jetzt'
        : ($startsIn->days >= 1
            ? ($start->isTomorrow() ? 'morgen' : 'in ' . $startsIn->days . ' Tagen')
            : ($startsIn->h >= 1 ? 'in ' . $startsIn->h . ' Stunde' . ($startsIn->h === 1 ? '' : 'n') : 'in ' . max(1, $startsIn->i) . ' Minuten'));
@endphp
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif; background-color: #f3f4f6; color: #333; line-height: 1.6;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 24px 0;">
    <tr>
        <td align="center" style="padding: 0 12px;">
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                <tr>
                    <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); background-color: #667eea; color: #ffffff; padding: 28px 24px; text-align: center;">
                        @if($event->organization?->logo)
                            <div style="margin-bottom: 12px;">
                                <img src="{{ asset('storage/' . $event->organization->logo) }}"
                                     alt="{{ $event->organization->name }} Logo"
                                     style="max-height: 40px; max-width: 150px; object-fit: contain; filter: brightness(0) invert(1);">
                            </div>
                        @endif
                        <h1 style="margin: 0; font-size: 24px;">⏰ Ihre Veranstaltung beginnt {{ $startsInText }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 24px;">
                        <p style="font-size: 16px; margin-top: 0;">Hallo {{ $recipientName }},</p>

                        <p style="font-size: 16px;">
                            wir möchten Sie an Ihre Teilnahme an folgender Veranstaltung erinnern:
                        </p>

                        <div style="background: #f9fafb; padding: 16px; border-radius: 8px; margin: 16px 0; border: 1px solid #e5e7eb;">
                            <h2 style="margin: 0 0 12px 0; color: #4f46e5; font-size: 20px;">{{ $event->title }}</h2>
                            <p style="margin: 4px 0;">📅 <strong>{{ $start->translatedFormat('l, d.m.Y') }}</strong></p>
                            <p style="margin: 4px 0;">🕐 {{ $start->format('H:i') }} Uhr @if($end) – {{ $end->format('H:i') }} Uhr @endif</p>
                            @if($session)
                                @php $allDates = $event->dates->where('is_cancelled', false)->values(); @endphp
                                <p style="margin: 4px 0;">🗓️ Termin {{ $allDates->search(fn ($d) => $d->id === $session->id) + 1 }} von {{ $allDates->count() }}</p>
                            @endif
                            @if($event->requiresVenue() && $venueSource->venue_name)
                                <p style="margin: 4px 0;">📍 {{ collect([$venueSource->venue_name, $venueSource->venue_address, trim($venueSource->venue_postal_code . ' ' . $venueSource->venue_city)])->filter()->implode(', ') }}</p>
                            @endif
                            @if($event->isOnline())
                                <p style="margin: 4px 0;">🌐 Online-Veranstaltung</p>
                            @elseif($event->isHybrid())
                                <p style="margin: 4px 0;">🎯 Hybrid – vor Ort oder online</p>
                            @endif
                            <p style="margin: 4px 0; color: #6b7280; font-size: 14px;">Buchungsnummer: {{ $booking->booking_number }}</p>
                        </div>

                        @if($event->requiresOnlineInfo())
                            @if($showOnlineAccess)
                                <div style="background: #eff6ff; padding: 16px; border-radius: 8px; margin: 16px 0; border-left: 4px solid #2563eb;">
                                    <p style="margin: 0 0 8px 0; font-weight: bold; color: #1e3a8a;">🔗 Ihre Zugangsdaten</p>
                                    <p style="margin: 0 0 12px 0;">
                                        <a href="{{ $event->online_url }}" style="color: #1d4ed8; word-break: break-all;">{{ $event->online_url }}</a>
                                    </p>
                                    @if($event->online_access_code)
                                        <p style="margin: 0 0 12px 0;">Zugangscode: <code style="background: #ffffff; padding: 2px 8px; border-radius: 4px; border: 1px solid #bfdbfe; font-family: monospace;">{{ $event->online_access_code }}</code></p>
                                    @endif
                                    <a href="{{ $event->online_url }}"
                                       style="display: inline-block; background: #2563eb; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;">
                                        Zur Online-Veranstaltung
                                    </a>
                                    <p style="margin: 12px 0 0 0; font-size: 13px; color: #475569;">Tipp: Wählen Sie sich einige Minuten vor Beginn ein, um Ton und Kamera zu prüfen.</p>
                                </div>
                            @else
                                <div style="background: #fffbeb; padding: 16px; border-radius: 8px; margin: 16px 0; border-left: 4px solid #f59e0b;">
                                    <p style="margin: 0;">ℹ️ Die Online-Zugangsdaten werden freigeschaltet, sobald Ihre Buchung bestätigt und bezahlt ist.
                                        Bitte wenden Sie sich bei Fragen an den Veranstalter.</p>
                                </div>
                            @endif
                        @endif

                        @if($event->requiresVenue())
                            <div style="background: #f0fdf4; padding: 16px; border-radius: 8px; margin: 16px 0; border-left: 4px solid #16a34a;">
                                @if($event->requires_ticket)
                                    <p style="margin: 0;">🎫 Bitte bringen Sie Ihr Ticket (ausgedruckt oder auf dem Smartphone) zur Veranstaltung mit.
                                        @if(!$isAttendee) Sie können es jederzeit über Ihre Buchung erneut herunterladen. @endif</p>
                                @else
                                    <p style="margin: 0;">🎫 Für den Einlass genügt Ihre Buchungsnummer <strong>{{ $booking->booking_number }}</strong>.</p>
                                @endif
                                @if($event->directions)
                                    <p style="margin: 8px 0 0 0; font-size: 14px;"><strong>Anfahrt:</strong> {{ Str::limit(strip_tags($event->directions), 300) }}</p>
                                @endif
                            </div>
                        @endif

                        @if($event->ticket_notes)
                            <div style="background: #f9fafb; padding: 16px; border-radius: 8px; margin: 16px 0; border: 1px solid #e5e7eb;">
                                <p style="margin: 0 0 4px 0; font-weight: bold;">Hinweise des Veranstalters</p>
                                <p style="margin: 0; font-size: 14px;">{!! nl2br(e($event->ticket_notes)) !!}</p>
                            </div>
                        @endif

                        <div style="text-align: center; margin: 24px 0;">
                            @unless($isAttendee)
                                <a href="{{ $booking->manageUrl() }}"
                                   style="display: inline-block; background: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 4px;">
                                    Buchung anzeigen
                                </a>
                            @endunless
                            <a href="{{ route('events.show', $event->slug) }}"
                               style="display: inline-block; background: #ffffff; color: #4f46e5; border: 1px solid #4f46e5; padding: 11px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 4px;">
                                Veranstaltungsdetails
                            </a>
                        </div>

                        <p style="font-size: 14px; color: #6b7280;">
                            Wir freuen uns auf Ihre Teilnahme!<br>
                            {{ $event->getOrganizerName() }}
                            @if($event->getOrganizerEmail())
                                · <a href="mailto:{{ $event->getOrganizerEmail() }}" style="color: #4f46e5;">{{ $event->getOrganizerEmail() }}</a>
                            @endif
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 16px 24px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 12px; color: #9ca3af;">
                        @if($isAttendee)
                            Sie erhalten diese E-Mail, weil Sie als Teilnehmer:in für diese Veranstaltung angemeldet wurden.
                        @else
                            Sie erhalten diese E-Mail, weil Sie diese Veranstaltung gebucht haben.
                            @if($booking->user_id)
                                <br><a href="{{ route('settings.notifications.edit') }}" style="color: #667eea;">E-Mail-Einstellungen verwalten</a>
                            @endif
                        @endif
                        <br>© {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
