<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort zurücksetzen</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <!-- Wrapper -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f3f4f6; padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">

                    <!-- Header with Gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                🔐 Passwort zurücksetzen
                            </h1>
                            <p style="margin: 10px 0 0 0; color: rgba(255,255,255,0.85); font-size: 16px;">
                                {{ config('app.name') }}
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                Hallo{{ isset($user) && $user->first_name ? ' ' . $user->first_name : '' }},
                            </p>

                            <!-- Main Message -->
                            <p style="margin: 0 0 25px 0; font-size: 16px; color: #374151; line-height: 1.6;">
                                wir haben eine Anfrage erhalten, das Passwort für Ihr Konto bei <strong>{{ config('app.name') }}</strong> zurückzusetzen.
                                Klicken Sie auf den folgenden Button, um ein neues Passwort festzulegen:
                            </p>

                            <!-- CTA Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 20px 0;">
                                        <a href="{{ $resetUrl }}"
                                           style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);">
                                            Neues Passwort festlegen
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <div style="background-color: #f9fafb; border-left: 4px solid #667eea; padding: 16px 20px; margin: 30px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
                                    <strong style="color: #374151;">⏱ Hinweis:</strong> Dieser Link ist aus Sicherheitsgründen
                                    <strong>{{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) }} Minuten</strong> gültig.
                                    Danach müssen Sie erneut eine Passwort-Zurücksetzen-Anfrage stellen.
                                </p>
                            </div>

                            <!-- Alternative Link -->
                            <p style="margin: 25px 0 0 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
                                Falls der Button nicht funktioniert, kopieren Sie bitte den folgenden Link in Ihren Browser:
                            </p>
                            <p style="margin: 10px 0 0 0; padding: 12px; background-color: #f9fafb; border-radius: 6px; word-break: break-all; font-size: 13px;">
                                <a href="{{ $resetUrl }}" style="color: #667eea; text-decoration: none;">
                                    {{ $resetUrl }}
                                </a>
                            </p>

                            <!-- Divider -->
                            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

                            <!-- Security Notice -->
                            <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 1.6;">
                                Sie haben keine Passwort-Zurücksetzen-Anfrage gestellt? Dann ignorieren Sie diese E-Mail bitte –
                                Ihr Passwort bleibt unverändert und Ihr Konto ist sicher.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 10px 0; font-size: 14px; color: #374151; line-height: 1.6;">
                                Viele Grüße<br>
                                <strong>Ihr {{ config('app.name') }}-Team</strong>
                            </p>
                            <p style="margin: 20px 0 0 0; font-size: 12px; color: #9ca3af; line-height: 1.5;">
                                © {{ date('Y') }} {{ config('app.name') }}. Alle Rechte vorbehalten.
                            </p>
                            <p style="margin: 10px 0 0 0; font-size: 12px; color: #9ca3af; line-height: 1.5;">
                                Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

