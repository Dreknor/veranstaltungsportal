<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Notifications\Channels\SafeWebPushChannel;
use App\Notifications\ConnectionRequestNotification;
use App\Notifications\EventCancelledNotification;
use App\Support\PushNotifications;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

// Beispielschlüssel aus RFC 8291 (gültiger P-256-Punkt, 16-Byte-Geheimnis)
const PUSH_TEST_P256DH = 'BCVxsr7N_eNgVRqvHtD0zTZsEc6-VV-JvLexhqUzORcxaOzi6-AYWXvTBHm4bjyPjs7Vd8pZGH6SRpkNtoIAiw4';
const PUSH_TEST_AUTH = 'BTBZMqHH6r4Tts7J_aSIgg';

function pushConfigure(): void
{
    config([
        'webpush.vapid.public_key' => 'BOx0vCq0aY5cY5mQ1T8jY7l8b4u1xg2J0bZ0b6sT9rS8n3m0t7d2F3k5w0e2Yh1k0W6o2bT1p9b4l3n8m0q6c1Y',
        'webpush.vapid.private_key' => 'k0W6o2bT1p9b4l3n8m0q6c1Yx3fJ7aB2cD4eF6gH8iJ',
    ]);
}

function pushUnconfigure(): void
{
    config(['webpush.vapid.public_key' => null, 'webpush.vapid.private_key' => null]);
}

function pushSubscriptionPayload(string $suffix = 'a'): array
{
    return [
        'endpoint' => 'https://push.example.test/send/'.$suffix,
        'keys' => ['p256dh' => PUSH_TEST_P256DH, 'auth' => PUSH_TEST_AUTH],
        'contentEncoding' => 'aes128gcm',
    ];
}

function pushUser(bool $enabled = true, bool $subscribed = true): User
{
    $user = User::factory()->create([
        'notification_preferences' => [PushNotifications::PREFERENCE => $enabled],
    ]);

    if ($subscribed) {
        $user->updatePushSubscription('https://push.example.test/send/'.$user->id, 'key', 'token', 'aes128gcm');
    }

    return $user->fresh();
}

function pushBooking(User $user): Booking
{
    $event = Event::factory()->create(['title' => 'Sommerfest im Park']);

    return Booking::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'customer_email' => $user->email,
    ]);
}

beforeEach(function () {
    pushUnconfigure();
});

// --- Anmelden / Abmelden eines Geräts ---

test('Gäste können kein Gerät für Push anmelden oder abmelden', function () {
    pushConfigure();

    $this->postJson(route('settings.notifications.push.store'), pushSubscriptionPayload())->assertUnauthorized();
    $this->deleteJson(route('settings.notifications.push.destroy'))->assertUnauthorized();
});

test('Anmelden speichert das Abo und schaltet Push ein', function () {
    pushConfigure();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('settings.notifications.push.store'), pushSubscriptionPayload())
        ->assertCreated()
        ->assertJson(['enabled' => true, 'devices' => 1]);

    $user->refresh();
    expect($user->pushSubscriptions)->toHaveCount(1)
        ->and($user->pushSubscriptions->first()->endpoint)->toBe('https://push.example.test/send/a')
        ->and($user->pushSubscriptions->first()->public_key)->toBe(PUSH_TEST_P256DH)
        ->and($user->notification_preferences[PushNotifications::PREFERENCE])->toBeTrue();
});

test('Anmelden prüft die Angaben des Browsers', function (array $payload, string $field) {
    pushConfigure();

    $this->actingAs(User::factory()->create())
        ->postJson(route('settings.notifications.push.store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'ohne Endpunkt' => [['keys' => ['p256dh' => 'a', 'auth' => 'b']], 'endpoint'],
    'Endpunkt ohne https' => [['endpoint' => 'http://push.example.test/x', 'keys' => ['p256dh' => 'a', 'auth' => 'b']], 'endpoint'],
    'ohne Schlüssel' => [['endpoint' => 'https://push.example.test/x'], 'keys'],
    'ohne auth' => [['endpoint' => 'https://push.example.test/x', 'keys' => ['p256dh' => 'a']], 'keys.auth'],
    'unbekannte Kodierung' => [['endpoint' => 'https://push.example.test/x', 'keys' => ['p256dh' => 'a', 'auth' => 'b'], 'contentEncoding' => 'gzip'], 'contentEncoding'],
]);

test('Anmelden weist unbrauchbare Schlüssel zurück, die beim Versand "Base64Url::encode()" auslösen würden', function (string $p256dh, string $auth, string $field) {
    pushConfigure();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('settings.notifications.push.store'), [
            'endpoint' => 'https://push.example.test/send/x',
            'keys' => ['p256dh' => $p256dh, 'auth' => $auth],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);

    expect($user->pushSubscriptions()->count())->toBe(0);
})->with([
    'Schlüssel kein Base64' => ['BPublicKeya', PUSH_TEST_AUTH, 'keys.p256dh'],
    'Schlüssel zu kurz' => ['AAAA', PUSH_TEST_AUTH, 'keys.p256dh'],
    'Auth kein Base64' => [PUSH_TEST_P256DH, 'authToken$a', 'keys.auth'],
    'Auth falsche Länge' => [PUSH_TEST_P256DH, 'AAAA', 'keys.auth'],
]);

test('Der Kanal fängt einen Fehler der Push-Bibliothek ab und liefert keine Berichte', function () {
    pushConfigure();
    $user = pushUser();
    \Illuminate\Support\Facades\Log::spy();
    $this->mock(WebPushChannel::class, fn ($mock) => $mock->shouldReceive('send')->once()->andThrow(new \TypeError('Base64Url::encode(): Argument #1 ($data) must be of type string, false given')));

    $result = app(SafeWebPushChannel::class)->send($user, new \App\Notifications\PushTestNotification);

    expect($result)->toBe([]);
});

test('Anmelden ist ohne VAPID-Schlüssel nicht möglich', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('settings.notifications.push.store'), pushSubscriptionPayload())
        ->assertStatus(503);

    expect($user->pushSubscriptions()->count())->toBe(0);
});

test('Abmelden eines Geräts lässt andere Geräte und Push eingeschaltet', function () {
    pushConfigure();
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->postJson(route('settings.notifications.push.store'), pushSubscriptionPayload('a'))->assertCreated();
    $this->postJson(route('settings.notifications.push.store'), pushSubscriptionPayload('b'))->assertCreated();

    $this->deleteJson(route('settings.notifications.push.destroy'), ['endpoint' => 'https://push.example.test/send/a'])
        ->assertOk()
        ->assertJson(['enabled' => true, 'devices' => 1]);

    expect($user->fresh()->notification_preferences[PushNotifications::PREFERENCE])->toBeTrue();
});

test('Abmelden des letzten Geräts schaltet Push aus', function () {
    pushConfigure();
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->postJson(route('settings.notifications.push.store'), pushSubscriptionPayload())->assertCreated();

    $this->deleteJson(route('settings.notifications.push.destroy'), ['endpoint' => 'https://push.example.test/send/a'])
        ->assertOk()
        ->assertJson(['enabled' => false, 'devices' => 0]);

    expect($user->fresh()->notification_preferences[PushNotifications::PREFERENCE])->toBeFalse();
});

test('Auf allen Geräten ausschalten entfernt alle Abos und leitet zurück', function () {
    $user = pushUser();
    $user->updatePushSubscription('https://push.example.test/send/zweites-geraet', 'key', 'token');

    $this->actingAs($user)
        ->from(route('settings.notifications.edit'))
        ->delete(route('settings.notifications.push.destroy'))
        ->assertRedirect(route('settings.notifications.edit'))
        ->assertSessionHas('success');

    expect($user->pushSubscriptions()->count())->toBe(0)
        ->and($user->fresh()->notification_preferences[PushNotifications::PREFERENCE])->toBeFalse();
});

test('Abmelden löscht keine Abos anderer Personen', function () {
    $other = pushUser();
    $endpoint = $other->pushSubscriptions->first()->endpoint;

    $this->actingAs(User::factory()->create())
        ->deleteJson(route('settings.notifications.push.destroy'), ['endpoint' => $endpoint])
        ->assertOk();

    expect($other->pushSubscriptions()->count())->toBe(1);
});

// --- Einstellung ---

test('Speichern der E-Mail-Einstellungen behält die Push-Einstellung', function () {
    $user = pushUser();

    $this->actingAs($user)
        ->put(route('settings.notifications.update'), ['email_booking_confirmed' => '1'])
        ->assertRedirect();

    $preferences = $user->fresh()->notification_preferences;
    expect($preferences[PushNotifications::PREFERENCE])->toBeTrue()
        ->and($preferences['email_booking_confirmed'])->toBeTrue()
        ->and($preferences['email_marketing'])->toBeFalse();
});

test('gespeicherte Einstellungen werden als Array gelesen', function () {
    $user = User::factory()->create(['notification_preferences' => ['email_marketing' => true]]);

    expect($user->fresh()->notification_preferences)->toBe(['email_marketing' => true]);
});

// --- Kanäle ---

test('Push-Kanal nur mit Schlüsseln, eingeschalteter Einstellung und angemeldetem Gerät', function (bool $configured, bool $enabled, bool $subscribed, bool $expected) {
    $configured ? pushConfigure() : pushUnconfigure();
    $user = pushUser($enabled, $subscribed);

    $channels = (new BookingConfirmedNotification(pushBooking($user)))->via($user);

    expect(in_array(SafeWebPushChannel::class, $channels, true))->toBe($expected)
        ->and($channels)->toContain('mail', 'database');
})->with([
    'alles erfüllt' => [true, true, true, true],
    'ohne VAPID-Schlüssel' => [false, true, true, false],
    'Push ausgeschaltet' => [true, false, true, false],
    'kein Gerät angemeldet' => [true, true, false, false],
]);

test('Push-Kanal nur zusätzlich zum Portal, nie für reine E-Mails', function () {
    pushConfigure();
    $user = pushUser();
    $booking = pushBooking($user);

    $mailOnly = new EventCancelledNotification($booking->event, $booking, ['mail']);
    $withDatabase = new EventCancelledNotification($booking->event, $booking, ['mail', 'database']);

    expect($mailOnly->via($user))->toBe(['mail'])
        ->and($withDatabase->via($user))->toContain(SafeWebPushChannel::class);
});

test('Versand über Notification::send nutzt den Push-Kanal', function () {
    pushConfigure();
    Notification::fake();
    $user = pushUser();

    $user->notify(new ConnectionRequestNotification(User::factory()->create()));

    Notification::assertSentTo($user, ConnectionRequestNotification::class,
        fn ($notification, array $channels) => in_array(SafeWebPushChannel::class, $channels, true));
});

// --- Nachricht ---

test('Push-Nachricht einer Buchungsbestätigung übernimmt Titel, Text und Ziel', function () {
    $user = pushUser();
    $booking = pushBooking($user);

    $message = (new BookingConfirmedNotification($booking))->toWebPush($user);
    $payload = $message->toArray();

    expect($message)->toBeInstanceOf(WebPushMessage::class)
        ->and($payload['title'])->toBe('Buchung bestätigt')
        ->and($payload['body'])->toBe('Ihre Buchung für "Sommerfest im Park" wurde bestätigt.')
        ->and($payload['data']['url'])->toBe(route('bookings.show', $booking->booking_number))
        ->and($payload['icon'])->toEndWith('/images/favicon/android-icon-192x192.png')
        ->and($payload['lang'])->toBe('de')
        ->and($message->getOptions())->toBe(['TTL' => PushNotifications::TTL_SECONDS]);
});

test('Push-Nachricht ohne Titel nutzt den Portalnamen und action_url', function () {
    $user = pushUser();
    $follower = User::factory()->create(['first_name' => 'Erika', 'last_name' => 'Muster']);

    $payload = (new ConnectionRequestNotification($follower))->toWebPush($user)->toArray();

    expect($payload['title'])->toBe(config('app.name'))
        ->and($payload['body'])->toBe($follower->fullName().' möchte sich mit Ihnen vernetzen.')
        ->and($payload['data']['url'])->toBe(url('/connections/requests'));
});

test('Push-Nachricht öffnet keine fremden Adressen und kürzt lange Texte', function () {
    $message = PushNotifications::messageFromData([
        'title' => '<b>Hinweis</b>',
        'message' => str_repeat('Sehr langer Text ', 30),
        'url' => 'https://example.org/anderswo',
    ])->toArray();

    expect($message['title'])->toBe('Hinweis')
        ->and(mb_strlen($message['body']))->toBeLessThanOrEqual(PushNotifications::BODY_LIMIT + 1)
        ->and($message['data']['url'])->toBe(route('notifications.index'));
});

// --- Robustheit ---

test('Fehler beim Push-Versand verhindern die Zustellung im Portal nicht', function () {
    pushConfigure();
    $user = pushUser();
    app()->bind(WebPushChannel::class, fn () => throw new RuntimeException('Push-Dienst nicht erreichbar'));

    $user->notify(new ConnectionRequestNotification(User::factory()->create()));

    expect($user->notifications()->count())->toBe(1);
});

test('Versand mit echten Schlüsseln läuft ohne Exception bis zum Push-Dienst', function () {
    $keys = \Minishlink\WebPush\VAPID::createVapidKeys();
    config(['webpush.vapid.public_key' => $keys['publicKey'], 'webpush.vapid.private_key' => $keys['privateKey']]);
    \Illuminate\Support\Facades\Exceptions::fake();
    \Illuminate\Support\Facades\Event::fake([\NotificationChannels\WebPush\Events\NotificationFailed::class]);

    // Schlüssel eines Browsers nachbilden; der "Push-Dienst" ist nicht erreichbar
    $details = openssl_pkey_get_details(openssl_pkey_new(['curve_name' => 'prime256v1', 'private_key_type' => OPENSSL_KEYTYPE_EC]));
    $b64 = fn (string $bytes) => rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    $publicKey = "\x04".str_pad($details['ec']['x'], 32, "\0", STR_PAD_LEFT).str_pad($details['ec']['y'], 32, "\0", STR_PAD_LEFT);

    $user = User::factory()->create(['notification_preferences' => [PushNotifications::PREFERENCE => true]]);
    $user->updatePushSubscription('http://127.0.0.1:9/push', $b64($publicKey), $b64(random_bytes(16)), 'aes128gcm');

    $user->notify(new ConnectionRequestNotification(User::factory()->create()));

    expect($user->notifications()->count())->toBe(1);
    \Illuminate\Support\Facades\Exceptions::assertNothingReported();
    \Illuminate\Support\Facades\Event::assertDispatched(\NotificationChannels\WebPush\Events\NotificationFailed::class);
});

// --- Einstellungsseite ---

test('Einstellungsseite zeigt ohne VAPID-Schlüssel den Hinweis statt der Schaltfläche', function (bool $enabled, bool $subscribed) {
    $user = pushUser($enabled, $subscribed);

    $this->actingAs($user)
        ->get(route('settings.notifications.edit'))
        ->assertOk()
        ->assertSee('Push-Benachrichtigungen')
        ->assertSee('data-push-state="not-configured"', false)
        ->assertSee('noch nicht eingerichtet')
        ->assertDontSee('Push einschalten')
        ->assertDontSee('in Entwicklung');
})->with([
    'nie eingeschaltet' => [false, false],
    'eingeschaltet mit Gerät' => [true, true],
    'eingeschaltet ohne Gerät' => [true, false],
]);

test('Einstellungsseite bietet mit VAPID-Schlüsseln das Einschalten an', function () {
    pushConfigure();
    $user = pushUser();

    $this->actingAs($user)
        ->get(route('settings.notifications.edit'))
        ->assertOk()
        ->assertSee('data-push-state="configured"', false)
        ->assertSee('Push einschalten')
        ->assertSee('Push ausschalten')
        ->assertSee('Im Browser')
        ->assertSee(hash('sha256', $user->pushSubscriptions->first()->endpoint))
        ->assertDontSee($user->pushSubscriptions->first()->endpoint)
        ->assertDontSee('noch nicht eingerichtet');
});

// --- Testnachricht ---

function pushReport(bool $success): \Minishlink\WebPush\MessageSentReport
{
    return new \Minishlink\WebPush\MessageSentReport(
        new \GuzzleHttp\Psr7\Request('POST', 'https://push.example.test/send/1'), null, $success, $success ? 'OK' : 'Gone'
    );
}

test('Gäste können keine Testnachricht senden', function () {
    pushConfigure();

    $this->postJson(route('settings.notifications.push.test'))->assertUnauthorized();
});

test('Testnachricht ohne VAPID-Schlüssel wird mit 422 abgelehnt', function () {
    $this->actingAs(pushUser())
        ->postJson(route('settings.notifications.push.test'))
        ->assertStatus(422)
        ->assertJson(['message' => 'Push-Benachrichtigungen sind für dieses Portal nicht eingerichtet.']);
});

test('Testnachricht ohne angemeldeten Browser wird mit 422 abgelehnt', function () {
    pushConfigure();

    $this->actingAs(pushUser(true, false))
        ->postJson(route('settings.notifications.push.test'))
        ->assertStatus(422)
        ->assertJsonPath('message', fn (string $message) => str_contains($message, 'kein Browser'));
});

test('Testnachricht geht nur über den Push-Kanal', function () {
    pushConfigure();
    \Illuminate\Support\Facades\Mail::fake();
    $user = pushUser();

    $this->mock(WebPushChannel::class, function ($mock) use ($user) {
        $mock->shouldReceive('send')->once()
            ->withArgs(fn ($notifiable, $notification) => $notifiable->is($user) && $notification instanceof \App\Notifications\PushTestNotification)
            ->andReturn([pushReport(true)]);
    });

    $this->actingAs($user)
        ->postJson(route('settings.notifications.push.test'))
        ->assertOk()
        ->assertJsonPath('message', fn (string $message) => str_contains($message, 'gesendet'));

    expect($user->notifications()->count())->toBe(0);
    \Illuminate\Support\Facades\Mail::assertNothingOutgoing();
});

test('Testnachricht: Kanäle und Inhalt', function () {
    $user = pushUser();
    $notification = new \App\Notifications\PushTestNotification;
    $payload = $notification->toWebPush($user)->toArray();

    expect($notification->via($user))->toBe([SafeWebPushChannel::class])
        ->and($payload['title'])->toBe('Testnachricht')
        ->and($payload['body'])->toBe('Push-Benachrichtigungen funktionieren auf diesem Gerät.')
        ->and($payload['data']['url'])->toBe(route('settings.notifications.edit'));
});

test('Testnachricht meldet einen Fehler, wenn kein Push-Dienst sie annimmt', function () {
    pushConfigure();
    $user = pushUser();
    $this->mock(WebPushChannel::class, fn ($mock) => $mock->shouldReceive('send')->once()->andReturn([pushReport(false)]));

    $this->actingAs($user)
        ->postJson(route('settings.notifications.push.test'))
        ->assertStatus(502)
        ->assertJsonPath('message', fn (string $message) => str_contains($message, 'nicht zugestellt'));
});

test('Einstellungsseite enthält die Testnachricht nur in der eingerichteten Fassung', function () {
    $user = pushUser();
    $this->actingAs($user)->get(route('settings.notifications.edit'))->assertDontSee('Testnachricht senden');

    pushConfigure();
    $this->actingAs($user)->get(route('settings.notifications.edit'))
        ->assertSee('Testnachricht senden')
        ->assertSee('\u0022testUrl\u0022', false)
        ->assertSee('E-Mail-Benachrichtigungen');
});
