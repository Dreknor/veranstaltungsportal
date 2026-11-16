<x-layouts.app title="Berechtigungs-Matrix">
    <x-ui.page-header title="Berechtigungs-Matrix" :back-href="route('admin.roles.index')" back-label="Zurück zu Rollen">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('admin.permissions.create')">Neue Berechtigung</x-ui.button>
            <x-ui.button :href="route('admin.roles.create')">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neue Rolle
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.table label="Berechtigungen je Rolle" table-class="min-w-full">
        <thead>
            <tr>
                <th scope="col" class="sticky left-0 z-10 min-w-56 bg-zone max-sm:min-w-40">Berechtigung</th>
                @foreach ($roles as $role)
                    <th scope="col" class="text-center">
                        {{ \App\Support\AccessLabels::role($role->name) }}
                        @if ($role->is_system)
                            <span class="block text-caption font-normal">System</span>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions as $group => $groupPermissions)
                <tr class="bg-zone hover:bg-zone">
                    <th scope="colgroup" colspan="{{ $roles->count() + 1 }}">
                        {{ \App\Support\AccessLabels::group($group) }}
                        <span class="font-normal text-muted tabular-nums">({{ $groupPermissions->count() }})</span>
                    </th>
                </tr>

                  @foreach ($groupPermissions as $permission)
                <tr>
                    <th scope="row" class="sticky left-0 z-10 bg-surface max-sm:min-w-40">
                        <span>{{ \App\Support\AccessLabels::permission($permission->name) }}</span>
                        @if ($permission->is_system)
                            <span class="text-caption font-normal text-muted">(System)</span>
                        @endif
                        @if ($permission->description)
                            <span class="block text-small font-normal text-muted">{{ $permission->description }}</span>
                        @endif
                    </th>
                    @foreach ($roles as $role)
                        <td class="text-center">
                            @if ($role->hasPermissionTo($permission->name))
                                <x-icon.check class="mx-auto h-5 w-5 text-ink" />
                                <span class="sr-only">Ja</span>
                            @else
                                <span aria-hidden="true" class="text-disabled">–</span>
                                <span class="sr-only">Nein</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            @endforeach
        </tbody>
    </x-ui.table>

    <p class="mt-4 flex flex-wrap items-center gap-x-6 gap-y-2 text-small text-muted">
        <span class="inline-flex items-center gap-2"><x-icon.check class="h-4 w-4 text-ink" /> Berechtigung
            vorhanden</span>
        <span class="inline-flex items-center gap-2"><span aria-hidden="true">–</span> Keine Berechtigung</span>
        <span>System: vom System geschützt</span>
    </p>
</x-layouts.app>
