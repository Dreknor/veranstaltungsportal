<x-layouts.app title="Benachrichtigungseinstellungen">
    <x-ui.page-header title="Einstellungen" class="mb-4" />

    @include('settings.partials.navigation')

    <div class="max-w-form">
        <form method="POST" action="{{ route('settings.notifications.update') }}">
            @csrf
            @method('PUT')

            <x-ui.section title="Benachrichtigungseinstellungen"
                description="Verwalten Sie Ihre E-Mail- und Push-Benachrichtigungen">
                <!-- Email Notifications -->
                <fieldset class="m-0 min-w-0 border-0 p-0">
                    <legend class="mb-2 p-0 text-h3">E-Mail-Benachrichtigungen</legend>
                    <div class="divide-y divide-line border-y border-line">
                        <x-ui.checkbox name="email_booking_confirmed" label="Buchungsbestätigung"
                            hint="Erhalten Sie eine E-Mail, wenn Ihre Buchung bestätigt wurde" :checked="$preferences['email_booking_confirmed'] ?? true" />
                        <x-ui.checkbox name="email_booking_cancelled" label="Stornierungsbestätigung"
                            hint="Erhalten Sie eine E-Mail, wenn eine Buchung storniert wurde" :checked="$preferences['email_booking_cancelled'] ?? true" />
                        <x-ui.checkbox name="email_event_reminder" label="Veranstaltungserinnerung"
                            hint="Erinnerung 24 Stunden vor Veranstaltungsbeginn" :checked="$preferences['email_event_reminder'] ?? true" />
                        <x-ui.checkbox name="email_event_updated" label="Veranstaltungsänderungen"
                            hint="Benachrichtigung bei Änderungen an gebuchten Veranstaltungen" :checked="$preferences['email_event_updated'] ?? true" />
                        <x-ui.checkbox name="email_new_review" label="Neue Bewertungen"
                            hint="Benachrichtigung bei neuen Bewertungen Ihrer Veranstaltungen (nur für Veranstalter)"
                            :checked="$preferences['email_new_review'] ?? false" />
                        <x-ui.checkbox name="email_marketing" label="Marketing & Newsletter"
                            hint="Informationen über neue Veranstaltungen und Angebote" :checked="$preferences['email_marketing'] ?? false" />
                    </div>
                </fieldset>

                <div class="mt-8">
                    <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
                </div>
            </x-ui.section>
        </form>

        {{-- Browser-Push: wird sofort wirksam, unabhängig vom Formular oben --}}
        <x-ui.section id="push-benachrichtigungen" title="Push-Benachrichtigungen" level="h2"
            class="mt-12 border-t border-line pt-8"
            description="Alle Benachrichtigungen, die im Portal erscheinen, zusätzlich als Mitteilung Ihres Browsers – auch wenn das Portal gerade nicht geöffnet ist.">
            @if (!$push['configured'])
                <x-ui.alert tone="info" data-push-state="not-configured">
                    <p>Push-Benachrichtigungen sind für dieses Portal noch nicht eingerichtet. Sie erhalten alle
                        Benachrichtigungen weiterhin im Portal und per E-Mail.</p>
                </x-ui.alert>
            @else
                <div x-data="pushNotifications(@js([
    'publicKey' => $push['publicKey'],
    'storeUrl' => route('settings.notifications.push.store'),
    'destroyUrl' => route('settings.notifications.push.destroy'),
    'testUrl' => route('settings.notifications.push.test'),
    'workerUrl' => asset('sw-push.js'),
    'devices' => $push['devices'],
    'knownEndpoints' => $push['knownEndpoints'],
]))" data-push-state="configured">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-y border-line py-4">
                        <div class="flex min-w-0 flex-col gap-1">
                            <p class="m-0 text-ui font-medium text-ink">Dieser Browser</p>
                            <p class="m-0 text-small text-muted">Push gilt jeweils für einen Browser auf einem Gerät.
                            </p>
                            <div aria-live="polite">
                                <x-ui.status x-show="state === 'checking'">Wird geprüft …</x-ui.status>
                                <x-ui.status tone="success" x-show="state === 'on'" x-cloak>Eingeschaltet</x-ui.status>
                                <x-ui.status x-show="state === 'off'" x-cloak>Ausgeschaltet</x-ui.status>
                                <x-ui.status tone="warning" x-show="state === 'denied'" x-cloak>Im Browser
                                    blockiert</x-ui.status>
                                <x-ui.status x-show="state === 'unsupported'" x-cloak>Nicht verfügbar</x-ui.status>
                            </div>
                        </div>
                        <div class="flex-none">
                            <x-ui.button x-show="state === 'off'" x-cloak x-on:click="enable()"
                                x-bind:disabled="busy">
                                Push einschalten
                            </x-ui.button>
                            <x-ui.button variant="secondary" x-show="state === 'on'" x-cloak x-on:click="disable()"
                                x-bind:disabled="busy">
                                Push ausschalten
                            </x-ui.button>
                        </div>
                    </div>

                    <div x-show="state === 'on'" x-cloak class="mt-2 flex flex-col items-start gap-1">
                        <x-ui.button variant="link" x-on:click="sendTest()" x-bind:disabled="busy">
                            Testnachricht senden
                        </x-ui.button>
                        <p x-show="testMessage" x-text="testMessage" x-cloak role="status"
                            x-bind:class="testStatus === 'success' ? 'text-success' : 'text-danger'"
                            class="m-0 text-small">
                        </p>
                    </div>

                    <p x-show="error" x-text="error" x-cloak role="alert" class="m-0 mt-3 text-small text-danger"></p>

                    <x-ui.alert tone="info" x-show="state === 'unsupported'" x-cloak class="mt-4">
                        <p>Dieser Browser unterstützt keine Push-Benachrichtigungen oder die Seite wird nicht über
                            eine sichere Verbindung (https) aufgerufen. Nutzen Sie dafür einen aktuellen Browser wie
                            Firefox, Chrome, Edge oder Safari. Ihre Benachrichtigungen erhalten Sie weiterhin im
                            Portal und per E-Mail.</p>
                    </x-ui.alert>

                    <x-ui.alert tone="warning" title="Benachrichtigungen sind im Browser blockiert"
                        x-show="state === 'denied'" x-cloak class="mt-4">
                        <p>So geben Sie sie wieder frei:</p>
                        <ul>
                            <li>Klicken Sie in der Adresszeile auf das Symbol links neben der Adresse.</li>
                            <li>Stellen Sie „Benachrichtigungen“ auf „Zulassen“.</li>
                            <li>Laden Sie diese Seite neu und schalten Sie Push ein.</li>
                        </ul>
                        <p class="mt-2">Auf dem Smartphone finden Sie die Einstellung in den Website-Einstellungen
                            Ihres Browsers.</p>
                    </x-ui.alert>

                    <div x-show="otherDevices > 0" x-cloak
                        class="mt-4 flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
                        <p class="m-0 text-small text-muted" x-text="otherDevicesText"></p>
                        <form method="POST" action="{{ route('settings.notifications.push.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <x-ui.button type="submit" variant="link" size="sm">Auf allen Geräten
                                ausschalten</x-ui.button>
                        </form>
                    </div>
                </div>
            @endif
        </x-ui.section>
    </div>
</x-layouts.app>
