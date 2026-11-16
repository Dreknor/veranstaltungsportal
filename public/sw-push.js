/*
 * Service Worker für Browser-Push-Benachrichtigungen.
 * Die Nutzlast baut App\Support\PushNotifications::messageFromData():
 * { title, body, icon, badge, lang, data: { url, id } }
 */

self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
    let payload = {};

    try {
        payload = event.data ? event.data.json() : {};
    } catch (error) {
        payload = { body: event.data ? event.data.text() : '' };
    }

    const title = payload.title || 'Neue Benachrichtigung';
    const options = {
        body: payload.body || '',
        icon: payload.icon || '/images/favicon/android-icon-192x192.png',
        badge: payload.badge || '/images/favicon/android-icon-96x96.png',
        lang: payload.lang || 'de',
        data: payload.data || {},
    };

    if (payload.tag) {
        options.tag = payload.tag;
    }

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const target = new URL(
        (event.notification.data && event.notification.data.url) ||
            '/notifications',
        self.location.origin,
    );

    // Nur Ziele im eigenen Portal öffnen
    if (target.origin !== self.location.origin) {
        return;
    }

    event.waitUntil(
        self.clients
            .matchAll({ type: 'window', includeUncontrolled: true })
            .then((windows) => {
                for (const client of windows) {
                    if (client.url === target.href && 'focus' in client) {
                        return client.focus();
                    }
                }

                return self.clients.openWindow(target.href);
            }),
    );
});
