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
                    <legend class="mb-2 p-0 text-h3">E-Mail Benachrichtigungen</legend>
                    <div class="divide-y divide-line border-y border-line">
                    <x-ui.checkbox name="email_booking_confirmed" label="Buchungsbestätigung" hint="Erhalten Sie eine E-Mail, wenn Ihre Buchung bestätigt wurde"
                        :checked="$preferences['email_booking_confirmed'] ?? true" />
                    <x-ui.checkbox name="email_booking_cancelled" label="Stornierungsbestätigung" hint="Erhalten Sie eine E-Mail, wenn eine Buchung storniert wurde"
                        :checked="$preferences['email_booking_cancelled'] ?? true" />
                    <x-ui.checkbox name="email_event_reminder" label="Veranstaltungserinnerung" hint="Erinnerung 24 Stunden vor Veranstaltungsbeginn"
                        :checked="$preferences['email_event_reminder'] ?? true" />
                    <x-ui.checkbox name="email_event_updated" label="Veranstaltungsänderungen" hint="Benachrichtigung bei Änderungen an gebuchten Veranstaltungen"
                        :checked="$preferences['email_event_updated'] ?? true" />
                    <x-ui.checkbox name="email_new_review" label="Neue Bewertungen" hint="Benachrichtigung bei neuen Bewertungen Ihrer Veranstaltungen (nur für Veranstalter)"
                        :checked="$preferences['email_new_review'] ?? false" />
                    <x-ui.checkbox name="email_marketing" label="Marketing & Newsletter" hint="Informationen über neue Veranstaltungen und Angebote"
                        :checked="$preferences['email_marketing'] ?? false" />
                    </div>
                </fieldset>

                <!-- Push Notifications -->
                <fieldset class="m-0 mt-8 min-w-0 border-0 p-0">
                    <legend class="mb-1 p-0 text-h3">Push-Benachrichtigungen</legend>
                    <p class="m-0 mb-2 text-small text-muted">Browser-Benachrichtigungen (in Entwicklung)</p>
                    <div class="divide-y divide-line border-y border-line">
                    <x-ui.checkbox name="push_booking_confirmed" label="Buchungsbestätigung" hint="Browser-Benachrichtigung bei Buchungsbestätigung"
                        :checked="$preferences['push_booking_confirmed'] ?? false" disabled />
                    <x-ui.checkbox name="push_event_reminder" label="Veranstaltungserinnerung" hint="Browser-Benachrichtigung vor Veranstaltungsbeginn"
                        :checked="$preferences['push_event_reminder'] ?? false" disabled />
                    <x-ui.checkbox name="push_event_updated" label="Veranstaltungsänderungen" hint="Browser-Benachrichtigung bei Änderungen"
                        :checked="$preferences['push_event_updated'] ?? false" disabled />
                    </div>
                </fieldset>

                <div class="mt-8">
                    <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
                </div>
            </x-ui.section>
        </form>
    </div>
</x-layouts.app>
