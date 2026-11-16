<x-layouts.app title="Team-Mitglieder importieren">
    <x-ui.page-header title="Team-Mitglieder importieren"
        subtitle="Importieren Sie mehrere Mitglieder auf einmal per CSV." :back-href="route('organizer.team.index')"
        back-label="Zurück zum Team" />

    @if (session('success') && session('import_errors') && count(session('import_errors')) > 0)
        <x-ui.alert tone="warning" class="mb-8">
            <details>
                <summary class="cursor-pointer font-medium">Fehler anzeigen ({{ count(session('import_errors')) }})
                </summary>
                <ul class="mt-2 list-disc pl-5">
                    @foreach (session('import_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </details>
        </x-ui.alert>
    @endif

    <div class="max-w-form">
        <x-ui.section title="Anleitung" class="border-t border-line py-8">
            <ol class="m-0 list-decimal pl-5 text-base text-ink">
                <li>Laden Sie die CSV-Vorlage herunter</li>
                <li>Tragen Sie die E-Mail-Adressen und Rollen der Mitglieder ein</li>
                <li>Speichern Sie die Datei im CSV-Format</li>
                <li>Laden Sie die Datei hier hoch</li>
            </ol>

            <x-ui.alert tone="info" title="Wichtig" class="mt-6">
                <ul class="m-0 list-disc pl-5">
                    <li>Die Benutzer müssen bereits registriert sein</li>
                    <li>Rollen können sein: <span class="font-medium">admin</span> oder <span
                            class="font-medium">member</span></li>
                    <li>Bereits vorhandene Mitglieder werden übersprungen</li>
                    <li>Alle importierten Mitglieder erhalten eine E-Mail-Benachrichtigung</li>
                </ul>
            </x-ui.alert>

            <div class="mt-6">
                <x-ui.button variant="secondary" :href="route('organizer.team.import.template')">
                    <x-slot:icon><x-icon.download /></x-slot:icon>
                    CSV-Vorlage herunterladen
                </x-ui.button>
            </div>
        </x-ui.section>

        <x-ui.section title="CSV-Datei hochladen" class="border-t border-line py-8">
            <form method="POST" action="{{ route('organizer.team.import.process') }}" enctype="multipart/form-data"
                class="flex flex-col gap-y-5">
                @csrf

                <p class="m-0 text-small text-muted">* Pflichtfeld</p>

                <div>
                    <x-ui.label for="csv_file" required>CSV-Datei</x-ui.label>
                    <x-ui.input type="file" name="csv_file" id="csv_file" accept=".csv,.txt" required
                        aria-describedby="csv_file-hint" />
                    <x-ui.hint id="csv_file-hint">Format: CSV mit Komma als Trennzeichen, höchstens 2 MB</x-ui.hint>
                    <x-ui.field-error name="csv_file" />
                </div>

                <x-ui.select label="Standard-Rolle (falls nicht in CSV angegeben)" name="default_role" required>
                    <option value="member" selected>Mitglied (eingeschränkte Berechtigungen)</option>
                    <option value="admin">Administrator (volle Berechtigungen)</option>
                </x-ui.select>

                <div class="flex flex-wrap gap-3">
                    <x-ui.button type="submit">Importieren</x-ui.button>
                    <x-ui.button variant="secondary" :href="route('organizer.team.index')">Abbrechen</x-ui.button>
                </div>
            </form>
        </x-ui.section>

        <x-ui.section title="Beispiel für das CSV-Format" level="h3" class="border-t border-line py-8">
            <pre class="font-sans relative m-0 overflow-x-auto rounded-control border border-line bg-zone p-4 text-small text-ink"><code>email,role
max@example.com,member
sarah@example.com,admin
tim@example.com,member</code></pre>
        </x-ui.section>
    </div>
</x-layouts.app>
