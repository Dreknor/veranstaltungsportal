<x-layouts.app :title="'Berechtigung bearbeiten: ' . $permission->name">
    <x-ui.page-header title="Berechtigung bearbeiten" :back-href="route('admin.permissions.index')" back-label="Zurück zu Berechtigungen">
        @if ($permission->is_system)
            <x-slot:meta>
                <x-ui.status tone="neutral">System-Berechtigung</x-ui.status>
            </x-slot:meta>
        @endif
        @if (!$permission->is_system)
            <x-slot:actions>
                <x-ui.button variant="danger" type="submit" form="delete-permission-form">
                    <x-slot:icon><x-icon.trash /></x-slot:icon>
                    Löschen
                </x-ui.button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    @if (!$permission->is_system)
        <form id="delete-permission-form" action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="hidden"
            onsubmit="return confirm('Permission \'{{ $permission->name }}\' wirklich löschen?')">
            @csrf
            @method('DELETE')
        </form>
    @endif

    <form method="POST" action="{{ route('admin.permissions.update', $permission) }}" class="max-w-form">
        @csrf
        @method('PUT')

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grunddaten" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                @if ($permission->is_system)
                    <div>
                        <x-ui.label for="permission-name-display" required>Name der Berechtigung</x-ui.label>
                        <x-ui.input id="permission-name-display" :value="$permission->name" disabled :old="false"
                            aria-describedby="permission-name-display-hint" />
                        <input type="hidden" name="name" value="{{ $permission->name }}">
                        <x-ui.hint id="permission-name-display-hint">System-Berechtigung: Der Name ist nicht änderbar.</x-ui.hint>
                    </div>
                @else
                    <x-ui.field label="Name der Berechtigung" name="name" :value="$permission->name" required />
                @endif

                <x-ui.field label="Gruppe" name="group" id="group-input" :value="$permission->group" required
                    list="group-suggestions" />
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
                                    data-group="{{ $g }}">{{ $g }}</x-ui.button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <x-ui.textarea label="Beschreibung" name="description" :value="$permission->description" optional rows="2" />
            </div>
        </x-ui.section>

        <x-ui.section title="Zugewiesen an Rollen" class="border-t border-line py-8">
            @forelse ($permission->roles as $role)
                @if ($loop->first)
                    <ul class="m-0 flex list-none flex-wrap gap-x-4 gap-y-2 p-0 text-base text-ink">
                @endif
                <li>{{ ucfirst($role->name) }}</li>
                @if ($loop->last)
                    </ul>
                @endif
            @empty
                <p class="m-0 text-base text-muted">Keiner Rolle zugewiesen.</p>
            @endforelse
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Speichern</x-ui.button>
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
