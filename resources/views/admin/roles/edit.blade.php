<x-layouts.app :title="'Rolle bearbeiten: ' . ucfirst($role->name)">
    <x-ui.page-header :title="'Rolle bearbeiten: ' . ucfirst($role->name)" :back-href="route('admin.roles.index')" back-label="Zurück zu Rollen">
        @if ($role->is_system)
            <x-slot:meta>
                <x-ui.status tone="neutral">System-Rolle</x-ui.status>
            </x-slot:meta>
        @endif
        @if (!$role->is_system)
            <x-slot:actions>
                <x-ui.button variant="danger" type="submit" form="delete-role-form">
                    <x-slot:icon><x-icon.trash /></x-slot:icon>
                    Rolle löschen
                </x-ui.button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    @if (!$role->is_system)
        <form id="delete-role-form" action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="hidden"
            onsubmit="return confirm('Rolle \'{{ $role->name }}\' wirklich löschen? {{ $role->users()->count() }} Benutzer werden auf \'user\' zurückgesetzt.')">
            @csrf
            @method('DELETE')
        </form>
    @endif

    <form method="POST" action="{{ route('admin.roles.update', $role) }}" class="max-w-form">
        @csrf
        @method('PUT')

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grunddaten" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                @if ($role->is_system)
                    <div>
                        <x-ui.label for="role-name-display" required>Rollenname</x-ui.label>
                        <x-ui.input id="role-name-display" :value="$role->name" disabled :old="false"
                            aria-describedby="role-name-display-hint" />
                        <input type="hidden" name="name" value="{{ $role->name }}">
                        <x-ui.hint id="role-name-display-hint">System-Rolle: Der Name ist nicht änderbar.</x-ui.hint>
                    </div>
                @else
                    <x-ui.field label="Rollenname" name="name" :value="$role->name" required
                        hint="Nur Kleinbuchstaben, Zahlen und Bindestriche." />
                @endif

                <x-ui.textarea label="Beschreibung" name="description" :value="$role->description" optional rows="2" />

                <fieldset class="m-0 min-w-0 border-0 p-0">
                    <x-ui.label as="legend">Farbe der Rolle</x-ui.label>
                    <div class="-ml-1 flex flex-wrap">
                        @foreach (['#6b7280', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#f97316'] as $color)
                            <label class="cursor-pointer p-1">
                                <input type="radio" name="color" value="{{ $color }}" class="peer sr-only"
                                    @checked(old('color', $role->color ?? '#6b7280') === $color)>
                                <span class="sr-only">{{ $color }}</span>
                                <span aria-hidden="true"
                                    class="block h-9 w-9 rounded-full outline-offset-2 peer-checked:outline peer-checked:outline-2 peer-checked:outline-ink peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-ink"
                                    style="background-color: {{ $color }};"></span>
                            </label>
                        @endforeach
                    </div>
                    <x-ui.field-error name="color" />
                </fieldset>
            </div>
        </x-ui.section>

        <x-ui.section title="Berechtigungen" class="border-t border-line py-8"
            :description="$role->name !== 'admin' ? count($rolePermissions) . ' von ' . $permissions->flatten()->count() . ' Berechtigungen ausgewählt' : null">
            @if ($role->name !== 'admin')
                <div class="flex flex-col gap-8">
                    @foreach ($permissions as $group => $groupPermissions)
                        <fieldset class="m-0 min-w-0 border-0 p-0">
                            <div class="mb-2 flex flex-wrap items-center justify-between gap-x-4 gap-y-1">
                                <legend class="float-left m-0 p-0 text-h3 capitalize">{{ $group }}</legend>
                                <x-ui.button variant="link" size="sm" class="select-group-btn"
                                    data-group="{{ $group }}">Alle wählen</x-ui.button>
                            </div>
                            <div class="clear-both grid grid-cols-1 gap-x-4 md:grid-cols-2">
                                @foreach ($groupPermissions as $permission)
                                    <x-ui.checkbox name="permissions[]" :value="$permission->name"
                                        :checked="in_array($permission->name, $rolePermissions)"
                                        data-perm="{{ Str::slug($group) }}"
                                        :hint="trim(($permission->description ?? '') . ($permission->is_system ? ' System-Berechtigung' : ''))"
                                        :label="$permission->name" />
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </div>
                <x-ui.field-error name="permissions" />
            @else
                <x-ui.alert tone="info">
                    Die <strong>Admin-Rolle</strong> erhält automatisch alle Berechtigungen und kann nicht eingeschränkt werden.
                </x-ui.alert>
            @endif
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Speichern</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.roles.index')">Abbrechen</x-ui.button>
        </div>
    </form>

    <div class="mt-8 max-w-form">
        <x-ui.section title="Statistiken" class="border-t border-line py-8">
            <x-ui.dl>
                <x-ui.dl-item label="Benutzer mit dieser Rolle" num>{{ $role->users()->count() }}</x-ui.dl-item>
                <x-ui.dl-item label="Berechtigungen" num>{{ $role->permissions->count() }}</x-ui.dl-item>
                <x-ui.dl-item label="System-Rolle">{{ $role->is_system ? 'Ja' : 'Nein' }}</x-ui.dl-item>
                <x-ui.dl-item label="Erstellt am" num>{{ $role->created_at->format('d.m.Y') }}</x-ui.dl-item>
            </x-ui.dl>
        </x-ui.section>

        <x-ui.section title="Aktuelle Berechtigungen" class="border-t border-line py-8">
            @forelse ($role->permissions as $permission)
                @if ($loop->first)
                    <ul class="m-0 flex list-none flex-wrap gap-x-4 gap-y-2 p-0 text-small text-ink">
                @endif
                <li class="font-mono">{{ $permission->name }}</li>
                @if ($loop->last)
                    </ul>
                @endif
            @empty
                <p class="m-0 text-small text-muted">Keine Berechtigungen zugewiesen.</p>
            @endforelse
        </x-ui.section>
    </div>

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
        </script>
    @endpush
</x-layouts.app>
