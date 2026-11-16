/**
 * Alpine-Komponente für Browser-Push in Einstellungen › Benachrichtigungen.
 *
 * Zustände: checking (Prüfung läuft) · unsupported (Browser kann kein Push) · denied (im Browser
 * blockiert) · off · on. "on" heißt: dieses Gerät hat ein Push-Abo, das der Server kennt.
 *
 * Optionen: publicKey (VAPID), storeUrl/destroyUrl (POST/DELETE), workerUrl (Service Worker),
 * devices (Anzahl angemeldeter Geräte), knownEndpoints (SHA-256 der Endpunkte auf dem Server).
 */
export function pushNotifications(options = {}) {
    return {
        state: 'checking',
        busy: false,
        error: '',
        devices: Number(options.devices || 0),

        get otherDevices() {
            return Math.max(0, this.devices - (this.state === 'on' ? 1 : 0));
        },

        get otherDevicesText() {
            const count = this.otherDevices;

            if (this.state === 'on') {
                return count === 1
                    ? 'Außerdem auf 1 weiteren Gerät eingeschaltet.'
                    : `Außerdem auf ${count} weiteren Geräten eingeschaltet.`;
            }

            return count === 1
                ? 'Auf 1 anderen Gerät eingeschaltet.'
                : `Auf ${count} anderen Geräten eingeschaltet.`;
        },

        async init() {
            if (!isSupported()) {
                this.state = 'unsupported';
                return;
            }

            if (Notification.permission === 'denied') {
                this.state = 'denied';
                return;
            }

            try {
                const registration = await this.registration();
                const subscription =
                    await registration.pushManager.getSubscription();

                if (!subscription) {
                    this.state = 'off';
                    return;
                }

                const known = (options.knownEndpoints || []).includes(
                    await sha256(subscription.endpoint),
                );

                // Abo ohne Gegenstück auf dem Server (z. B. "Auf allen Geräten ausschalten") oder mit
                // altem Schlüssel: im Browser aufräumen, damit der Zustand eindeutig ist.
                if (!known || !sameKey(subscription, options.publicKey)) {
                    await subscription.unsubscribe();
                    this.state = 'off';
                    return;
                }

                this.state =
                    Notification.permission === 'granted' ? 'on' : 'off';
            } catch (error) {
                this.state = 'off';
            }
        },

        registration() {
            return navigator.serviceWorker
                .register(options.workerUrl, { scope: '/' })
                .then(() => navigator.serviceWorker.ready);
        },

        async enable() {
            this.busy = true;
            this.error = '';

            try {
                const permission = await Notification.requestPermission();

                if (permission === 'denied') {
                    this.state = 'denied';
                    return;
                }

                if (permission !== 'granted') {
                    this.error =
                        'Die Anfrage des Browsers wurde geschlossen. Bitte erlauben Sie Benachrichtigungen, um Push einzuschalten.';
                    return;
                }

                const registration = await this.registration();
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: base64UrlToUint8Array(
                        options.publicKey,
                    ),
                });

                const response = await send(options.storeUrl, 'POST', {
                    ...subscription.toJSON(),
                    contentEncoding: contentEncoding(),
                });

                if (!response.ok) {
                    await subscription.unsubscribe();
                    throw new Error(String(response.status));
                }

                this.devices = Number(
                    (await response.json()).devices || this.devices + 1,
                );
                this.state = 'on';
            } catch (error) {
                this.error =
                    'Push konnte nicht eingeschaltet werden. Bitte versuchen Sie es später erneut.';
            } finally {
                this.busy = false;
            }
        },

        async disable() {
            this.busy = true;
            this.error = '';

            try {
                const registration = await this.registration();
                const subscription =
                    await registration.pushManager.getSubscription();

                if (subscription) {
                    const endpoint = subscription.endpoint;
                    await subscription.unsubscribe();

                    const response = await send(options.destroyUrl, 'DELETE', {
                        endpoint,
                    });
                    if (response.ok) {
                        this.devices = Number(
                            (await response.json()).devices || 0,
                        );
                    }
                }

                this.state = 'off';
            } catch (error) {
                this.error =
                    'Push konnte nicht ausgeschaltet werden. Bitte versuchen Sie es später erneut.';
            } finally {
                this.busy = false;
            }
        },
    };
}

function isSupported() {
    return (
        window.isSecureContext &&
        'serviceWorker' in navigator &&
        'PushManager' in window &&
        'Notification' in window
    );
}

function send(url, method, body) {
    return fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN':
                document.querySelector('meta[name="csrf-token"]')?.content ||
                '',
        },
        body: JSON.stringify(body),
    });
}

function contentEncoding() {
    const supported = window.PushManager?.supportedContentEncodings || [];

    return supported.includes('aes128gcm') || supported.length === 0
        ? 'aes128gcm'
        : supported[0];
}

function sameKey(subscription, publicKey) {
    const current = subscription.options?.applicationServerKey;
    if (!current || !publicKey) {
        return true;
    }

    const expected = base64UrlToUint8Array(publicKey);
    const actual = new Uint8Array(current);

    return (
        expected.length === actual.length &&
        expected.every((byte, index) => byte === actual[index])
    );
}

function base64UrlToUint8Array(value) {
    const padding = '='.repeat((4 - (value.length % 4)) % 4);
    const base64 = (value + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = window.atob(base64);

    return Uint8Array.from(raw, (char) => char.charCodeAt(0));
}

async function sha256(value) {
    const digest = await crypto.subtle.digest(
        'SHA-256',
        new TextEncoder().encode(value),
    );

    return Array.from(new Uint8Array(digest), (byte) =>
        byte.toString(16).padStart(2, '0'),
    ).join('');
}
