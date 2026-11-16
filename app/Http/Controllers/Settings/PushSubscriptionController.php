<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\PushNotifications;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Minishlink\WebPush\ContentEncoding;
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
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
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

    private function setPreference(User $user, bool $enabled): void
    {
        $preferences = $user->notification_preferences;
        $preferences[PushNotifications::PREFERENCE] = $enabled;

        $user->forceFill(['notification_preferences' => $preferences])->save();
    }
}
