<x-layouts.app title="Datenschutz & DSGVO">
    <x-ui.page-header title="Datenschutz & DSGVO" subtitle="Ihre Daten gehören Ihnen: Laden Sie sie herunter, steuern Sie ihre Verwendung oder lassen Sie Ihr Konto löschen." />

    <div class="max-w-prose">
        <section aria-labelledby="export" class="border-t border-line py-8">
            <h2 id="export" class="m-0 mb-2 text-h3">Datenexport (DSGVO Art. 15)</h2>
            <p class="m-0 mb-4 text-base text-muted">
                Laden Sie eine vollständige Kopie Ihrer persönlichen Daten herunter. Dies umfasst alle Ihre Buchungen,
                erstellten Veranstaltungen, Bewertungen, Favoriten, Abzeichen, Verbindungen und Benachrichtigungen.
            </p>
            <x-ui.button variant="secondary" :href="route('data-privacy.export')">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                Daten als JSON exportieren
            </x-ui.button>
        </section>

        <section aria-labelledby="files" class="border-t border-line py-8">
            <h2 id="files" class="m-0 mb-2 text-h3">Dateien herunterladen</h2>
            <p class="m-0 mb-4 text-base text-muted">
                Laden Sie alle Ihre hochgeladenen Dateien herunter, einschließlich Profilbild,
                Teilnahmezertifikate und andere Dokumente als ZIP-Archiv.
            </p>
            <x-ui.button variant="secondary" :href="route('data-privacy.download-files')">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                Alle Dateien herunterladen (ZIP)
            </x-ui.button>
        </section>

        <section aria-labelledby="settings" class="border-t border-line py-8">
            <h2 id="settings" class="m-0 mb-2 text-h3">Datenschutzeinstellungen</h2>
            <p class="m-0 mb-4 text-base text-muted">
                Passen Sie Ihre Datenschutzeinstellungen an und steuern Sie, wie Ihre Daten verwendet werden.
                Verwalten Sie Netzwerk-Einstellungen, Profil-Sichtbarkeit und Datenanalyse-Präferenzen.
            </p>
            <x-ui.button variant="secondary" :href="route('settings.privacy.edit')">
                <x-slot:icon><x-icon.settings /></x-slot:icon>
                Einstellungen verwalten
            </x-ui.button>
        </section>

        <section aria-labelledby="deletion" class="border-t border-line py-8">
            <h2 id="deletion" class="m-0 mb-2 text-h3">Konto löschen (DSGVO Art. 17)</h2>
            <p class="m-0 mb-4 text-base text-muted">
                Beantragen Sie die vollständige Löschung Ihres Kontos und aller damit verbundenen Daten.
                Diese Aktion kann nicht rückgängig gemacht werden.
            </p>

            <x-ui.alert tone="warning" title="Wichtige Hinweise" class="mb-4">
                <ul class="m-0 list-disc pl-5">
                    <li>Sie können Ihr Konto nicht löschen, wenn Sie bevorstehende Veranstaltungen als Veranstalter haben.</li>
                    <li>Sie können Ihr Konto nicht löschen, wenn Sie bevorstehende Buchungen haben.</li>
                    <li>Alle Ihre Daten werden innerhalb von 30 Tagen vollständig gelöscht.</li>
                    <li>Diese Aktion kann nicht rückgängig gemacht werden.</li>
                </ul>
            </x-ui.alert>

            <x-ui.modal name="delete-account" title="Konto löschen" :show="$errors->has('password') || $errors->has('reason')">
                <x-slot:trigger>
                    <x-ui.button variant="danger">
                        <x-slot:icon><x-icon.trash /></x-slot:icon>
                        Konto löschen beantragen
                    </x-ui.button>
                </x-slot:trigger>

                <p class="m-0 mb-5 text-base text-muted">
                    Sind Sie sicher, dass Sie Ihr Konto löschen möchten? Bitte geben Sie Ihr Passwort ein, um fortzufahren.
                </p>
                <form id="delete-account-form" action="{{ route('data-privacy.request-deletion') }}" method="POST" class="flex flex-col gap-5">
                    @csrf
                    <x-ui.field label="Passwort" name="password" type="password" required autocomplete="current-password" :old="false" />
                    <x-ui.textarea label="Grund" name="reason" optional rows="3" placeholder="Warum möchten Sie Ihr Konto löschen?" />
                </form>

                <x-slot:footer>
                    <x-ui.button variant="secondary" x-on:click="close()">Abbrechen</x-ui.button>
                    <x-ui.button variant="danger" type="submit" form="delete-account-form">Konto löschen</x-ui.button>
                </x-slot:footer>
            </x-ui.modal>
        </section>
    </div>
</x-layouts.app>
