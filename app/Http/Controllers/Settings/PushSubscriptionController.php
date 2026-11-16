<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\Channels\SafeWebPushChannel;
use App\Notifications\PushTestNotification;
use App\Support\PushNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Minishlink\WebPush\ContentEncoding;
use Minishlink\WebPush\MessageSentReport;
use NotificationChannels\WebPush\PushSubscription;

/**
 * Anmelden und Abmelden eines Geräts (Browsers) für Push-Benachrichtigungen.
 *
 * Die Einstellung "push_enabled" in notification_preferences folgt den Geräten: Anmelden schaltet
 * Push ein, sind keine Geräte mehr angemeldet, ist Push aus.
 */
class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        abort_unless(PushNotifications::configured(), 503, 'Push-Benachrichtigungen sind nicht eingerichtet.');

        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'url:https', 'max:'.PushSubscription::ENDPOINT_MAX_LENGTH],
            'keys' => ['required', 'array'],
            // Browser liefern den öffentlichen Schlüssel (65 Byte, unkomprimierter P-256-Punkt) und das
            // Auth-Geheimnis (16 Byte) base64url-kodiert. Anderes lässt sich später nicht verschlüsseln
            // (Fehler "Base64Url::encode(): Argument #1 must be of type string, false given" beim Versand).
            'keys.p256dh' => ['required', 'string', 'max:255', fn ($attribute, $value, $fail) => self::decodedLength($value) === 65 && str_starts_with((string) self::decode($value), "\x04") ?: $fail('Der Schlüssel des Browsers ist ungültig.')],
            'keys.auth' => ['required', 'string', 'max:255', fn ($attribute, $value, $fail) => self::decodedLength($value) === 16 ?: $fail('Das Auth-Geheimnis des Browsers ist ungültig.')],
            'contentEncoding' => ['nullable', 'string', Rule::enum(ContentEncoding::class)],
        ]);

        /** @var User $user */
        $user = $request->user();

        $user->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['contentEncoding'] ?? ContentEncoding::aes128gcm->value,
        );

        $this->setPreference($user, true);

        return response()->json([
            'enabled' => true,
            'devices' => $user->pushSubscriptions()->count(),
        ], 201);
    }

    /**
     * Mit endpoint: nur dieses Gerät abmelden. Ohne: alle Geräte abmelden.
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'endpoint' => ['nullable', 'string', 'max:'.PushSubscription::ENDPOINT_MAX_LENGTH],
        ]);

        /** @var User $user */
        $user = $request->user();

        if (filled($validated['endpoint'] ?? null)) {
            $user->deletePushSubscription($validated['endpoint']);
        } else {
            $user->pushSubscriptions()->delete();
        }

        $devices = $user->pushSubscriptions()->count();
        $this->setPreference($user, $devices > 0);

        if ($request->expectsJson()) {
            return response()->json(['enabled' => $devices > 0, 'devices' => $devices]);
        }

        return back()->with('success', 'Push-Benachrichtigungen wurden auf allen Geräten ausgeschaltet.');
    }

    /**
     * Testnachricht an alle angemeldeten Browser der Person – nur per Push.
     */
    public function test(Request $request, SafeWebPushChannel $channel): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (! PushNotifications::configured()) {
            return response()->json(['message' => 'Push-Benachrichtigungen sind für dieses Portal nicht eingerichtet.'], 422);
        }

        if (! $user->pushSubscriptions()->exists()) {
            return response()->json(['message' => 'Für Ihr Konto ist kein Browser für Push angemeldet. Bitte schalten Sie Push zuerst ein.'], 422);
        }

        // Direkt über den Kanal, damit das Ergebnis der Push-Dienste zurückgemeldet werden kann
        $reports = $channel->send($user, new PushTestNotification);
        $sent = collect($reports)->contains(fn ($report) => $report instanceof MessageSentReport && $report->isSuccess());

        if (! $sent) {
            return response()->json(['message' => 'Die Testnachricht konnte nicht zugestellt werden. Bitte versuchen Sie es später erneut.'], 502);
        }

        return response()->json(['message' => 'Testnachricht wurde gesendet. Sie sollte in wenigen Sekunden erscheinen.']);
    }

    private function setPreference(User $user, bool $enabled): void
    {
        $preferences = $user->notification_preferences;
        $preferences[PushNotifications::PREFERENCE] = $enabled;

        $user->forceFill(['notification_preferences' => $preferences])->save();
    }

    private static function decode(mixed $value): string|false
    {
        if (! is_string($value) || $value === '' || preg_match('/^[A-Za-z0-9_-]+={0,2}$/', $value) !== 1) {
            return false;
        }

        $value = rtrim(strtr($value, '-_', '+/'), '=');

        return base64_decode($value.str_repeat('=', (4 - strlen($value) % 4) % 4), true);
    }

    private static function decodedLength(mixed $value): ?int
    {
        $decoded = self::decode($value);

        return $decoded === false ? null : strlen($decoded);
    }
}
