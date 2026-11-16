<x-layouts.app title="Rollen & Berechtigungen">
    <x-ui.page-header title="Rollen & Berechtigungen" :back-href="route('admin.dashboard')" back-label="Zum Dashboard">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.roles.matrix')">Matrix-Ansicht</x-ui.button>
            <x-ui.button variant="link" :href="route('admin.permissions.index')">Berechtigungen verwalten</x-ui.button>
            <x-ui.button :href="route('admin.roles.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neue Rolle
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.section title="Rollen" :description="$roles->count() . ' gesamt'" class="mb-12">
        <x-ui.table label="Rollen">
            <thead>
                <tr>
                    <th scope="col">Rolle</th>
                    <th scope="col" class="max-sm:hidden">Berechtigungen</th>
                    <th scope="col" class="max-sm:hidden num">Benutzer</th>
                    <th scope="col" class="max-sm:hidden num">Rechte gesamt</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <th scope="row">
                            <span class="block">{{ \App\Support\AccessLabels::role($role->name) }}</span>
                            @if ($role->description)
                                <span
                                    class="block max-w-prose text-small font-normal text-muted">{{ $role->description }}</span>
                            @endif
                            @if ($role->is_system)
                                <x-ui.status tone="neutral" class="mt-1">System-Rolle</x-ui.status>
                            @endif
                        </th>
                        <td class="max-sm:hidden text-small">
                            {{ $role->permissions->take(4)->map(fn($permission) => \App\Support\AccessLabels::permission($permission->name))->implode(', ') }}
                            @if ($role->permissions->count() > 4)
                                <span class="text-muted">+{{ $role->permissions->count() - 4 }} mehr</span>
                            @endif
                        </td>
                        <td class="max-sm:hidden num">{{ $role->users_count }}</td>
                        <td class="max-sm:hidden num">{{ $role->permissions->count() }}</td>
                        <td class="actions">
                            <x-ui.button size="sm" variant="secondary" :href="route('admin.roles.edit', $role)">Bearbeiten</x-ui.button>
                            @if (!$role->is_system)
                                <x-ui.button size="sm" variant="secondary" type="submit"
                                    form="delete-role-{{ $role->id }}">Löschen</x-ui.button>
                                <form id="delete-role-{{ $role->id }}"
                                    action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="hidden"
                                    onsubmit="return confirm('Rolle „{{ \App\Support\AccessLabels::role($role->name) }}“ wirklich löschen? {{ $role->users_count }} Benutzer werden auf „Teilnehmer“ zurückgesetzt.')">
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

    <x-ui.section title="Berechtigungen" :description="$permissions->flatten()->count() . ' gesamt'" link-label="Alle verwalten" :link-href="route('admin.permissions.index')">
        <div class="flex flex-col gap-8">
            @foreach ($permissions as $group => $groupPermissions)
                <div class="border-t border-line pt-4">
                    <h3 class="m-0 mb-3 text-h3">
                        {{ \App\Support\AccessLabels::group($group) }}
                        <span
                            class="text-small font-normal text-muted tabular-nums">({{ $groupPermissions->count() }})</span>
                    </h3>
                    <ul
                        class="m-0 grid list-none grid-cols-1 gap-x-4 gap-y-2 p-0 text-small sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($groupPermissions as $permission)
                            <li class="text-ink">
                                {{ \App\Support\AccessLabels::permission($permission->name) }}
                                @if ($permission->is_system)
                                    <span class="text-muted">(System)</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </x-ui.section>
</x-layouts.app>
