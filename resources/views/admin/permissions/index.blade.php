<x-layouts.app title="Berechtigungen verwalten">
    <x-ui.page-header title="Berechtigungen verwalten" :subtitle="$permissions->flatten()->count() . ' Berechtigungen gesamt'" :back-href="route('admin.roles.index')" back-label="Zurück zu Rollen">
        <x-slot:actions>
            <x-ui.button :href="route('admin.permissions.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neue Berechtigung
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-8 flex flex-wrap gap-2" role="group" aria-label="Nach Gruppe filtern">
        <x-ui.button variant="secondary" size="sm"
            class="group-filter-btn aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold"
            aria-pressed="true" data-group="all">
            Alle ({{ $permissions->flatten()->count() }})
        </x-ui.button>
        @foreach ($groups as $group)
            <x-ui.button variant="secondary" size="sm"
                class="group-filter-btn aria-pressed:border-ink aria-pressed:bg-zone aria-pressed:font-semibold"
                aria-pressed="false" data-group="{{ $group }}">
                {{ \App\Support\AccessLabels::group($group) }} ({{ $permissions->get($group)?->count() ?? 0 }})
            </x-ui.button>
        @endforeach
    </div>

    <div class="flex flex-col gap-12">
        @foreach ($permissions as $group => $groupPermissions)
            <x-ui.section :title="\App\Support\AccessLabels::group($group)" :description="$groupPermissions->count() . ' Berechtigungen'" class="group-section" data-group="{{ $group }}">
                <x-ui.table :label="'Berechtigungen der Gruppe ' . \App\Support\AccessLabels::group($group)">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col" class="max-sm:hidden">Beschreibung</th>
                            <th scope="col" class="max-sm:hidden">Rollen</th>
                            <th scope="col" class="max-sm:hidden">System</th>
                            <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupPermissions as $permission)
                            <tr>
                                <th scope="row">
                                    <span
                                        class="block">{{ \App\Support\AccessLabels::permission($permission->name) }}</span>
                                    <span class="block text-small font-normal text-muted">{{ $permission->name }}</span>
                                </th>
                                <td class="max-sm:hidden text-muted">{{ $permission->description ?? '–' }}</td>
                                <td class="max-sm:hidden">
                                    @foreach ($permission->roles->take(3) as $role)
                                        {{ \App\Support\AccessLabels::role($role->name) }}@if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                    @if ($permission->roles->count() > 3)
                                        <span class="text-muted">+{{ $permission->roles->count() - 3 }} mehr</span>
                                    @endif
                                    @if ($permission->roles->isEmpty())
                                        <span class="text-muted">keine</span>
                                    @endif
                                </td>
                                <td class="max-sm:hidden">{{ $permission->is_system ? 'System' : '–' }}</td>
                                <td class="actions">
                                    <x-ui.button size="sm" variant="secondary"
                                        :href="route('admin.permissions.edit', $permission)">Bearbeiten</x-ui.button>
                                    @if (!$permission->is_system)
                                        <x-ui.button size="sm" variant="secondary" type="submit" class="max-sm:hidden"
                                            form="delete-permission-{{ $permission->id }}">Löschen</x-ui.button>
                                        <form id="delete-permission-{{ $permission->id }}"
                                            action="{{ route('admin.permissions.destroy', $permission) }}"
                                            method="POST" class="hidden"
                                            onsubmit="return confirm('Berechtigung „{{ \App\Support\AccessLabels::permission($permission->name) }}“ wirklich löschen? Sie ist {{ trans_choice('{0} keiner Rolle|{1} einer Rolle|[2,*] :count Rollen', $permission->roles->count()) }} zugewiesen.')">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table>
            </x-ui.section>
        @endforeach
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.group-filter-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const group = this.dataset.group;

                    // Aktiven Button hervorheben
                    document.querySelectorAll('.group-filter-btn').forEach(b => {
                        b.setAttribute('aria-pressed', 'false');
                    });
                    this.setAttribute('aria-pressed', 'true');

                    // Gruppen-Sektionen filtern
                    document.querySelectorAll('.group-section').forEach(section => {
                        if (group === 'all' || section.dataset.group === group) {
                            section.style.display = '';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.app>
