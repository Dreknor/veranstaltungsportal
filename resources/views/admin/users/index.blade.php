<x-layouts.app title="Benutzerverwaltung" :header="false">
    <x-ui.page-header title="Benutzerverwaltung" subtitle="Verwalten Sie alle Benutzer im System." />

    <form action="{{ route('admin.users.index') }}" method="GET"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Name oder E-Mail" />
        <x-ui.select label="Rolle" name="role" :old="false"
            :options="['' => 'Alle', 'organizer' => 'Veranstalter', 'admin' => 'Administratoren']"
            :selected="request('role')" />
        <x-ui.select label="SSO-Provider" name="sso_provider" :old="false">
            <option value="">Alle</option>
            <option value="none" @selected(request('sso_provider') === 'none')>Nur normale Benutzer</option>
            @foreach ($ssoProviders ?? [] as $provider)
                <option value="{{ $provider }}" @selected(request('sso_provider') === $provider)>{{ ucfirst($provider) }}</option>
            @endforeach
        </x-ui.select>
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button type="submit" variant="secondary" size="lg">Filtern</x-ui.button>
            <x-ui.button variant="link" :href="route('admin.users.index')">Zurücksetzen</x-ui.button>
        </div>
    </form>

    <x-ui.table label="Benutzer">
        <thead>
            <tr>
                <th scope="col">Benutzer</th>
                <th scope="col">Rolle</th>
                <th scope="col" class="num">Veranstaltungen</th>
                <th scope="col" class="num">Buchungen</th>
                <th scope="col">Registriert</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <th scope="row">
                        <span class="block">
                            {{ $user->name }}
                            @if ($user->isSsoUser())
                                <span class="ml-1 text-small font-normal text-muted" title="SSO-Benutzer via {{ $user->ssoProviderName() }}">({{ $user->ssoProviderName() }})</span>
                            @endif
                        </span>
                        <span class="block text-small font-normal text-muted">{{ $user->email }}</span>
                        <span class="mt-0.5 block">
                            @if ($user->hasVerifiedEmail())
                                <x-ui.status tone="success">E-Mail verifiziert</x-ui.status>
                            @else
                                <x-ui.status tone="warning">Nicht verifiziert</x-ui.status>
                            @endif
                        </span>
                    </th>
                    <td>
                        @if ($user->roles->count() > 0)
                            <span class="flex flex-col items-start gap-1">
                                @foreach ($user->roles as $role)
                                    <x-ui.status tone="neutral">{{ ucfirst($role->name) }}</x-ui.status>
                                @endforeach
                            </span>
                        @else
                            <span class="text-muted">Keine Rolle</span>
                        @endif
                    </td>
                    <td class="num">{{ $user->events_count }}</td>
                    <td class="num">{{ $user->bookings_count }}</td>
                    <td class="tabular-nums whitespace-nowrap">{{ $user->created_at->format('d.m.Y') }}</td>
                    <td class="actions">
                        <x-ui.dropdown label="Aktionen" width="w-64">
                            <x-ui.dropdown-item :href="route('admin.users.edit', $user)">Bearbeiten</x-ui.dropdown-item>
                            @if (auth()->id() !== $user->id)
                                @if (!$user->hasRole('admin'))
                                    <x-ui.dropdown-item type="submit" form="impersonate-{{ $user->id }}">Verkörpern</x-ui.dropdown-item>
                                @endif
                                @if (!$user->hasRole('organizer') && !$user->hasRole('admin'))
                                    <x-ui.dropdown-item type="submit" form="promote-{{ $user->id }}">Zum Veranstalter machen</x-ui.dropdown-item>
                                @elseif ($user->hasRole('organizer') && !$user->hasRole('admin'))
                                    <x-ui.dropdown-item type="submit" form="demote-{{ $user->id }}">Zum Teilnehmer machen</x-ui.dropdown-item>
                                @endif
                                <x-ui.dropdown-item type="submit" form="toggle-admin-{{ $user->id }}">{{ $user->hasRole('admin') ? 'Admin-Rolle entfernen' : 'Zum Administrator machen' }}</x-ui.dropdown-item>
                                <x-ui.dropdown-item danger type="submit" form="destroy-{{ $user->id }}">Löschen</x-ui.dropdown-item>
                            @endif
                        </x-ui.dropdown>
                        @if (auth()->id() !== $user->id)
                            @if (!$user->hasRole('admin'))
                                <form id="impersonate-{{ $user->id }}" action="{{ route('admin.users.impersonate', $user) }}" method="POST" class="hidden">@csrf</form>
                            @endif
                            @if (!$user->hasRole('organizer') && !$user->hasRole('admin'))
                                <form id="promote-{{ $user->id }}" action="{{ route('admin.users.promote-organizer', $user) }}" method="POST" class="hidden">@csrf</form>
                            @elseif ($user->hasRole('organizer') && !$user->hasRole('admin'))
                                <form id="demote-{{ $user->id }}" action="{{ route('admin.users.demote-participant', $user) }}" method="POST"
                                    onsubmit="return confirm('Organisator-Rechte wirklich entfernen?');" class="hidden">@csrf</form>
                            @endif
                            <form id="toggle-admin-{{ $user->id }}" action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="hidden">@csrf</form>
                            <form id="destroy-{{ $user->id }}" action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                onsubmit="return confirm('Benutzer wirklich löschen?');" class="hidden">@csrf @method('DELETE')</form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="6" class="p-0">
                        <x-ui.empty :bordered="false">Keine Benutzer gefunden.</x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</x-layouts.app>
