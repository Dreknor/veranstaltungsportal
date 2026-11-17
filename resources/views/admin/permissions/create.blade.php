<x-layouts.app title="Neue Berechtigung erstellen">
    <x-ui.page-header title="Neue Berechtigung erstellen" :back-href="route('admin.permissions.index')" back-label="Zurück zu Berechtigungen" />

    <form method="POST" action="{{ route('admin.permissions.store') }}" class="max-w-form">
        @csrf

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grunddaten" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Name der Berechtigung" name="name" required placeholder="z. B. manage invoices"
                    hint="Nur Kleinbuchstaben, Zahlen, Leerzeichen und Bindestriche. Konvention: „Verb Nomen“ (z. B. „view events“)." />

                <x-ui.field label="Gruppe" name="group" id="group-input" required placeholder="z. B. invoices"
                    list="group-suggestions" hint="Bestehende Gruppe auswählen oder neue eingeben." />
                <datalist id="group-suggestions">
                    @foreach ($groups as $g)
                        <option value="{{ $g }}">
                    @endforeach
                </datalist>

                @if ($groups->isNotEmpty())
                    <div>
                        <p class="m-0 mb-2 text-small text-muted">Vorhandene Gruppen:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($groups as $g)
                                <x-ui.button variant="secondary" size="sm" class="group-quick-btn"
                                    :data-group="($g)">{{ \App\Support\AccessLabels::group($g) }}</x-ui.button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <x-ui.textarea label="Beschreibung" name="description" optional rows="2"
                    placeholder="Was erlaubt diese Berechtigung?" />
            </div>
        </x-ui.section>

        <x-ui.alert tone="info" class="mb-8">
            Die neue Berechtigung wird automatisch der <strong>Admin-Rolle</strong> zugewiesen.
        </x-ui.alert>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Berechtigung erstellen</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.permissions.index')">Abbrechen</x-ui.button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.querySelectorAll('.group-quick-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('group-input').value = this.dataset.group;
                });
            });
        </script>
    @endpush
</x-layouts.app>
