<x-layouts.app title="Datenschutz-Einstellungen">
    <x-ui.page-header title="Einstellungen" class="mb-4" />

    @include('settings.partials.navigation')

    <div class="max-w-form">
        <form action="{{ route('settings.privacy.update') }}" method="POST">
            @csrf
            @method('PUT')

            <x-ui.section title="Datenschutz-Einstellungen" description="Steuern Sie, wer was von Ihrem Profil sehen kann">
                <!-- Network & Connections Section -->
                <h3 class="m-0 mb-2 text-h3">Netzwerk & Verbindungen</h3>
                <div class="divide-y divide-line border-y border-line">
                <x-ui.checkbox name="allow_networking" label="Vernetzung erlauben"
                    hint="Andere Benutzer können mit Ihnen in Kontakt treten, Ihnen Verbindungsanfragen senden und Sie zu ihrem Netzwerk hinzufügen"
                    :checked="$user->allow_networking ?? true" />
                <x-ui.checkbox name="show_profile_public" label="Öffentliches Profil"
                    hint="Ihr Profil ist für alle Benutzer sichtbar, auch wenn sie nicht mit Ihnen verbunden sind"
                    :checked="$user->show_profile_public ?? false" />
                </div>

                <!-- Contact Information Section -->
                <h3 class="m-0 mb-2 mt-8 text-h3">Kontaktinformationen</h3>
                <div class="divide-y divide-line border-y border-line">
                <x-ui.checkbox name="show_email_to_connections" label="E-Mail an Verbindungen zeigen"
                    hint="Nur Nutzer, mit denen Sie verbunden sind, können Ihre E-Mail-Adresse sehen"
                    :checked="(bool) $user->show_email_to_connections" />
                <x-ui.checkbox name="show_phone_to_connections" label="Telefonnummer an Verbindungen zeigen"
                    hint="Nur Nutzer, mit denen Sie verbunden sind, können Ihre Telefonnummer sehen"
                    :checked="(bool) $user->show_phone_to_connections" />
                </div>

                <!-- Datenverarbeitung -->
                <h3 class="m-0 mb-2 mt-8 text-h3">Datenverarbeitung &amp; Nutzungsanalyse</h3>
                <div class="divide-y divide-line border-y border-line">
                <x-ui.checkbox name="allow_data_analytics" label="Datenanalyse erlauben"
                    hint="Ihre anonymisierten Nutzungsdaten helfen uns, die Plattform zu verbessern"
                    :checked="$user->allow_data_analytics ?? true" />
                </div>

                <div class="mt-8">
                    <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
                </div>
            </x-ui.section>
        </form>

        <!-- DSGVO Rights Info -->
        <x-ui.section title="Ihre Rechte nach DSGVO" level="h2" class="mt-12 border-t border-line pt-8">
            <p class="m-0 mb-3 text-base text-ink">Sie haben folgende Rechte bezüglich Ihrer persönlichen Daten:</p>
            <ul class="m-0 list-disc space-y-2 pl-5 text-base text-ink">
                <li><strong>Recht auf Auskunft (Art. 15):</strong> Datenexport unter <a href="{{ route('data-privacy.index') }}" class="font-medium underline underline-offset-[3px]">Meine Daten</a></li>
                <li><strong>Recht auf Berichtigung (Art. 16):</strong> Profil-Einstellungen bearbeiten</li>
                <li><strong>Recht auf Löschung (Art. 17):</strong> Kontolöschung unter <a href="{{ route('data-privacy.index') }}" class="font-medium underline underline-offset-[3px]">Meine Daten</a></li>
                <li><strong>Recht auf Datenübertragbarkeit (Art. 20):</strong> Export als JSON/ZIP</li>
            </ul>
        </x-ui.section>

        <!-- Info Box -->
        <x-ui.alert tone="info" title="Über Ihre Privatsphäre" class="mt-8">
            <p>
                Ihre Privatsphäre ist uns wichtig. Sie haben jederzeit die volle Kontrolle darüber,
                wer welche Informationen von Ihnen sehen kann. Blockierte Nutzer können Ihr Profil
                nicht einsehen.
            </p>
        </x-ui.alert>
    </div>
</x-layouts.app>
