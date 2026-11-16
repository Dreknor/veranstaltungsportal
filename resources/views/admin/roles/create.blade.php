<x-layouts.app title="Neue Rolle erstellen">
    <x-ui.page-header title="Neue Rolle erstellen" :back-href="route('admin.roles.index')" back-label="Zurück zu Rollen" />

    <form method="POST" action="{{ route('admin.roles.store') }}" class="max-w-form">
        @csrf

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grunddaten" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Rollenname" name="name" required placeholder="z. B. content-editor"
                    hint="Nur Kleinbuchstaben, Zahlen und Bindestriche erlaubt." />
                <x-ui.textarea label="Beschreibung" name="description" optional rows="2"
                    placeholder="Kurze Beschreibung der Rolle" />
                @include('admin.roles._color-field', ['current' => 'neutral'])
            </div>
        </x-ui.section>

        <x-ui.section title="Berechtigungen zuweisen" class="border-t border-line py-8">
            <x-slot:actions>
                <x-ui.button variant="secondary" size="sm" id="selectAllBtn">Alle wählen</x-ui.button>
            </x-slot:actions>

            <div class="flex flex-col gap-8">
                @foreach ($permissions as $group => $groupPermissions)
                    <fieldset class="m-0 min-w-0 border-0 p-0">
                        <div class="mb-2 flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
                            <legend class="float-left m-0 p-0 text-h3">{{ \App\Support\AccessLabels::group($group) }}
                            </legend>
                            <x-ui.button variant="link" size="sm" class="select-group-btn"
                                data-group="{{ $group }}">Alle wählen</x-ui.button>
                        </div>
                        <div class="clear-both grid grid-cols-1 gap-x-4 md:grid-cols-2">
                            @foreach ($groupPermissions as $permission)
                                <x-ui.checkbox name="permissions[]" :value="$permission->name" :checked="is_array(old('permissions')) &&
                                    in_array($permission->name, old('permissions'))"
                                    data-perm="{{ Str::slug($group) }}" :hint="trim(
                                        ($permission->description ?? '') .
                                            ($permission->is_system ? ' System-Berechtigung' : ''),
                                    )" :label="\App\Support\AccessLabels::permission($permission->name)" />
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach
            </div>
            <x-ui.field-error name="permissions" />
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Rolle erstellen</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.roles.index')">Abbrechen</x-ui.button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.querySelectorAll('.select-group-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const group = this.dataset.group;
                    const slug = group.replace(/[^a-z0-9]/gi, '-').toLowerCase();
                    const checkboxes = document.querySelectorAll('[data-perm="' + slug + '"]');
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    checkboxes.forEach(c => c.checked = !allChecked);
                    this.textContent = allChecked ? 'Alle wählen' : 'Keine wählen';
                });
            });

            document.getElementById('selectAllBtn').addEventListener('click', function() {
                const all = document.querySelectorAll('[data-perm]');
                const allChecked = Array.from(all).every(c => c.checked);
                all.forEach(c => c.checked = !allChecked);
                this.textContent = allChecked ? 'Alle wählen' : 'Keine wählen';
            });
        </script>
    @endpush
</x-layouts.app>
