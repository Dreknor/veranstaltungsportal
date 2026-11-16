{{--
    Gemeinsames Layout aller handgebauten E-Mails (Richtlinie B7).

    Verwendung: @extends('emails.layout') und diese Abschnitte:
      title        Titel des Dokuments (Pflicht)
      preheader    optional: Vorschautext, den Mail-Programme neben dem Betreff zeigen
      header_logo  optional: Logo des Veranstalters rechts im Kopf (nur <img>)
      heading      optional: Überschrift (h1) über dem Inhalt
      subheading   optional: Zeile unter der Überschrift (muted)
      content      Inhalt
      footer_note  optional: Hinweis im Fuß (Grund der E-Mail, Abmelden, Einstellungen)

    Bausteine: emails.partials.button (Button als <a> in Tabelle) und
    emails.partials.notice (Hinweis-/Statuskasten mit den Meldungsfarben aus A2).

    Farben nur aus A2: ink #1E3135, primary #2B4347, muted #55676A, line #DCE3E2,
    leaf #8DC11D (nur im Zeichen der Wortmarke). Keine Bilder von außen für die Wortmarke.
--}}
@php
    $mailFont = '-apple-system, \'Segoe UI\', Roboto, Arial, sans-serif';
    $appName = config('app.name', 'Bildungsportal');
    $siteUrl = url('/');
    $siteLabel = rtrim(preg_replace('#^https?://(www\.)?#', '', $siteUrl), '/');
@endphp
<!DOCTYPE html>
<html lang="de" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title', $appName)</title>
</head>

<body
    style="margin: 0; padding: 0; width: 100%; background-color: #FFFFFF; color: #1E3135; font-family: {{ $mailFont }}; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    @hasSection('preheader')
        <div
            style="display: none; max-height: 0; overflow: hidden; mso-hide: all; font-size: 1px; line-height: 1px; color: #FFFFFF;">
            @yield('preheader')</div>
    @endif
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
        style="width: 100%; background-color: #FFFFFF;">
        <tr>
            <td align="center" style="padding: 0 16px;">
                <!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="width: 100%; max-width: 600px;">

                    {{-- Kopf: Wortmarke auf Weiß, darunter Linie --}}
                    <tr>
                        <td style="padding: 24px 0 16px 0; border-bottom: 1px solid #DCE3E2;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="middle" style="vertical-align: middle;">
                                        <a href="{{ $siteUrl }}" style="text-decoration: none; color: #1E3135;">
                                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                                <tr>
                                                    <td width="36" height="36" align="center" valign="middle"
                                                        style="width: 36px; height: 36px; background-color: #2B4347; border-radius: 18px; font-size: 0; line-height: 0; text-align: center; vertical-align: middle;">
                                                        <span
                                                            style="display: inline-block; width: 14px; height: 14px; background-color: #8DC11D; border-radius: 14px 0 14px 0; vertical-align: middle;"></span>
                                                    </td>
                                                    <td
                                                        style="padding-left: 12px; font-family: {{ $mailFont }}; vertical-align: middle;">
                                                        <span
                                                            style="display: block; font-size: 17px; line-height: 20px; font-weight: 600; letter-spacing: -0.01em; color: #1E3135;">{{ $appName }}</span>
                                                        <span
                                                            style="display: block; font-size: 13px; line-height: 16px; color: #55676A;">Hauptfach
                                                            Mensch</span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </a>
                                    </td>
                                    @hasSection('header_logo')
                                        <td align="right" valign="middle"
                                            style="vertical-align: middle; text-align: right; padding-left: 16px;">
                                            @yield('header_logo')
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Inhalt --}}
                    <tr>
                        <td
                            style="padding: 32px 0; font-family: {{ $mailFont }}; font-size: 16px; line-height: 1.5; color: #1E3135;">
                            @hasSection('heading')
                                <h1
                                    style="margin: 0 0 8px 0; font-family: {{ $mailFont }}; font-size: 24px; line-height: 1.25; font-weight: 600; letter-spacing: -0.015em; color: #1E3135;">
                                    @yield('heading')</h1>
                            @endif
                            @hasSection('subheading')
                                <p style="margin: 0 0 8px 0; font-size: 15px; line-height: 1.4; color: #55676A;">
                                    @yield('subheading')</p>
                            @endif
                            @hasSection('heading')
                                <div style="height: 16px; line-height: 16px; font-size: 16px;">&nbsp;</div>
                            @endif
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Fuß --}}
                    <tr>
                        <td
                            style="padding: 24px 0 32px 0; border-top: 1px solid #DCE3E2; font-family: {{ $mailFont }}; font-size: 13px; line-height: 1.5; color: #55676A;">
                            @hasSection('footer_note')
                                <p style="margin: 0 0 12px 0; font-size: 13px; line-height: 1.5; color: #55676A;">
                                    @yield('footer_note')</p>
                            @endif
                            <p style="margin: 0; font-size: 13px; line-height: 1.5; color: #55676A;">
                                © {{ date('Y') }} {{ $appName }} · Ein Angebot der
                                <a href="https://www.esdigmbh.de/"
                                    style="color: #55676A; text-decoration: underline;">ESdi GmbH</a>
                            </p>
                            <p style="margin: 4px 0 0 0; font-size: 13px; line-height: 1.5; color: #55676A;">
                                <a href="{{ $siteUrl }}"
                                    style="color: #55676A; text-decoration: underline;">{{ $siteLabel }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>

</html>
