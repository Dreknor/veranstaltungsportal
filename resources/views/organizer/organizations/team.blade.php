<x-layouts.app title="Team verwalten">
    <x-ui.page-header title="Team der Organisation" subtitle="Verwalten Sie Teammitglieder und deren Rollen.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.organization.edit')">
                <x-slot:icon><x-icon.settings /></x-slot:icon>
                Organisation bearbeiten
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if (session('warning'))
        <x-ui.alert tone="warning" dismissible class="mb-8">
            <p class="m-0">
                Möchten Sie automatisch ein Konto für diesen Benutzer erstellen? Der Benutzer erhält eine E-Mail mit den
                Zugangsdaten und der Möglichkeit, die Registrierung rückgängig zu machen.
            </p>
            <form method="POST" action="{{ route('organizer.team.invite') }}" class="mt-3">
                @csrf
                <input type="hidden" name="email" value="{{ session('pending_email') }}">
                <input type="hidden" name="role" value="{{ session('pending_role') }}">
                <input type="hidden" name="create_account" value="1">
                <x-ui.button type="submit" variant="secondary">Ja, Konto erstellen und einladen</x-ui.button>
            </form>
        </x-ui.alert>
    @endif

    <x-ui.section title="Mitglied einladen" class="mb-12">
        <form method="POST" action="{{ route('organizer.team.invite') }}"
            class="grid max-w-form grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
            @csrf
            <x-ui.field label="E-Mail des Benutzers" name="email" type="email" required
                placeholder="benutzer@beispiel.de" />
            <x-ui.select label="Rolle" name="role" required>
                <option value="admin" @selected(old('role') === 'admin')>{{ \App\Models\Organization::roleLabel('admin') }}
                </option>
                <option value="member" @selected(old('role') === 'member')>Mitglied</option>
            </x-ui.select>
            <div class="col-span-full">
                <x-ui.button type="submit">Einladen / Hinzufügen</x-ui.button>
            </div>
        </form>
    </x-ui.section>

    <x-ui.section title="Mitglieder">
        <x-ui.table label="Mitglieder der Organisation">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col" class="max-sm:hidden">E-Mail</th>
                    <th scope="col">Rolle</th>
                    <th scope="col" class="max-sm:hidden">Beigetreten</th>
                    <th scope="col" class="actions max-sm:hidden">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    <tr>
                        <th scope="row">
                            <span class="flex items-center gap-3">
                                <img src="{{ $member->profilePhotoUrl() }}"
                                    class="h-10 w-10 shrink-0 max-sm:hidden rounded-full border border-line object-cover"
                                    alt="">
                                <span>{{ $member->fullName() }}<span
                                        class="block break-all text-small font-normal text-muted sm:hidden">{{ $member->email }}</span></span>
                            </span>
                        </th>
                        <td class="max-sm:hidden">{{ $member->email }}</td>
                        <td>
                            <form method="POST"
                                action="{{ route('organizer.team.update-role', ['user' => $member->id]) }}"
                                class="flex flex-col items-start gap-2 sm:flex-row sm:items-center">
                                @csrf
                                @method('PUT')
                                <x-ui.select name="role" id="role-{{ $member->id }}" size="sm"
                                    :old="false" aria-label="Rolle von {{ $member->fullName() }}"
                                    class="min-w-36 max-sm:min-w-28">
                                    <option value="owner" @selected($member->pivot->role === 'owner')>
                                        {{ \App\Models\Organization::roleLabel('owner') }}</option>
                                    <option value="admin" @selected($member->pivot->role === 'admin')>
                                        {{ \App\Models\Organization::roleLabel('admin') }}</option>
                                    <option value="member" @selected($member->pivot->role === 'member')>
                                        {{ \App\Models\Organization::roleLabel('member') }}</option>
                                </x-ui.select>
                                <x-ui.button type="submit" size="sm" variant="secondary">Speichern</x-ui.button>
                            </form>
                            {{-- Schmale Bildschirme: Entfernen steht unter der Rolle statt in einer eigenen Spalte --}}
                            <div class="mt-2 sm:hidden">
                                @if ($member->pivot->role !== 'owner')
                                    <form method="POST"
                                        action="{{ route('organizer.team.remove', ['user' => $member->id]) }}"
                                        onsubmit="return confirm('Mitglied wirklich entfernen?');">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" size="sm"
                                            variant="danger">Entfernen</x-ui.button>
                                    </form>
                                @else
                                    <span class="text-small text-muted">Haupt-Inhaber</span>
                                @endif
                            </div>
                        </td>
                        <td class="tabular-nums max-sm:hidden">
                            {{ optional($member->pivot->joined_at)->format('d.m.Y') ?? '–' }}</td>
                        <td class="actions max-sm:hidden">
                            @if ($member->pivot->role !== 'owner')
                                <form method="POST"
                                    action="{{ route('organizer.team.remove', ['user' => $member->id]) }}"
                                    onsubmit="return confirm('Mitglied wirklich entfernen?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-ui.button type="submit" size="sm" variant="danger">Entfernen</x-ui.button>
                                </form>
                            @else
                                <span class="text-small text-muted">Haupt-Inhaber</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="5" class="p-0">
                            <x-ui.empty :bordered="false">Noch keine Mitglieder vorhanden.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>
</x-layouts.app>
