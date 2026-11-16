<x-layouts.app title="Benutzer bearbeiten" :header="false">
    <x-ui.page-header title="Benutzer bearbeiten" :subtitle="$user->fullName() . ' · ' . $user->email" :back-href="route('admin.users.index')" back-label="Zur Benutzerliste" />

    <div class="grid items-start gap-x-12 gap-y-12 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between">

        {{-- Stammdaten --}}
        <section aria-labelledby="stammdaten">
            <h2 id="stammdaten" class="m-0 mb-2 text-h2-form">Stammdaten</h2>
            <p class="mb-6 mt-0 text-small text-muted">* Pflichtfeld</p>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="flex flex-col gap-y-5">
                @csrf
                @method('PUT')

                <x-ui.field label="Anzeigename" name="name" :value="$user->name" required />

                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Vorname" name="first_name" :value="$user->first_name" optional />
                    <x-ui.field label="Nachname" name="last_name" :value="$user->last_name" optional />
                </div>

                <x-ui.field label="E-Mail-Adresse" name="email" type="email" :value="$user->email" required
                    :hint="$user->hasVerifiedEmail()
                        ? 'Verifiziert am ' . $user->email_verified_at->format('d.m.Y H:i') . ' Uhr'
                        : 'Noch nicht verifiziert'" />

                <x-ui.field label="Telefon" name="phone" :value="$user->phone" optional />

                <fieldset class="m-0 min-w-0 border-0 p-0">
                    <x-ui.label as="legend">Rollen</x-ui.label>
                    @foreach ($roles as $role)
                        @php $lockedAdmin = auth()->id() === $user->id && $role->name === 'admin'; @endphp
                        <x-ui.checkbox name="roles[]" :value="$role->name" :checked="in_array($role->name, $userRoles)" :disabled="$lockedAdmin"
                            :label="\App\Support\AccessLabels::role($role->name) .
                                ($lockedAdmin ? ' (kann nicht geändert werden)' : '')" />
                        @if ($lockedAdmin && in_array($role->name, $userRoles))
                            <input type="hidden" name="roles[]" value="admin">
                        @endif
                    @endforeach
                </fieldset>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <x-ui.button type="submit">Speichern</x-ui.button>
                    <x-ui.button variant="secondary" :href="route('admin.users.index')">Abbrechen</x-ui.button>
                    @if ($user->hasRole('organizer'))
                        <x-ui.button variant="link" :href="route('admin.organizer-fees.edit', $user)">Individuelle Gebühren</x-ui.button>
                    @endif
                </div>
            </form>
        </section>

        {{-- Aktionen --}}
        <div class="flex flex-col gap-8">

            <section aria-labelledby="verifizierung" class="border-t border-line pt-6">
                <h2 id="verifizierung" class="m-0 mb-2 text-h3">E-Mail-Verifizierung</h2>

                @if ($user->hasVerifiedEmail())
                    <p class="mb-4 mt-0 text-small text-muted">
                        Die E-Mail ist verifiziert ({{ $user->email_verified_at->format('d.m.Y') }}).
                    </p>
                    <form action="{{ route('admin.users.unverify-email', $user) }}" method="POST"
                        onsubmit="return confirm('Verifizierung wirklich aufheben?');">
                        @csrf
                        <x-ui.button type="submit" variant="secondary" full>Verifizierung aufheben</x-ui.button>
                    </form>
                @else
                    <p class="mb-4 mt-0 text-small text-muted">Die E-Mail ist noch nicht verifiziert.</p>
                    <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="secondary" full>E-Mail als verifiziert
                            markieren</x-ui.button>
                    </form>
                @endif
            </section>

            <section aria-labelledby="passwort" class="border-t border-line pt-6">
                <h2 id="passwort" class="m-0 mb-2 text-h3">Passwort zurücksetzen</h2>

                @if (!$user->isSsoUser())
                    <p class="mb-4 mt-0 text-small text-muted">
                        Setzen Sie direkt ein neues Passwort für diesen Benutzer. Der Benutzer wird ausgeloggt.
                    </p>

                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                        x-data="{ show: false }" class="flex flex-col gap-y-4">
                        @csrf
                        <x-ui.field label="Neues Passwort" name="password" type="password"
                            x-bind:type="show ? 'text' : 'password'" :old="false" required minlength="8"
                            autocomplete="new-password" />
                        <x-ui.field label="Passwort bestätigen" name="password_confirmation" type="password"
                            x-bind:type="show ? 'text' : 'password'" :old="false" required minlength="8"
                            autocomplete="new-password" />
                        <x-ui.checkbox x-model="show" label="Passwort anzeigen" size="small" />
                        <x-ui.button type="submit" variant="secondary" full
                            onclick="return confirm('Passwort wirklich zurücksetzen? Der Benutzer wird ausgeloggt.')">Passwort
                            zurücksetzen</x-ui.button>
                    </form>
                @else
                    <x-ui.alert tone="info" size="small">
                        Dieser Benutzer verwendet SSO ({{ $user->ssoProviderName() }}) und hat kein lokales Passwort.
                    </x-ui.alert>
                @endif
            </section>

            <section aria-labelledby="benutzerinfo" class="border-t border-line pt-6">
                <h2 id="benutzerinfo" class="m-0 mb-4 text-h3">Benutzerinfo</h2>
                <dl class="m-0 flex flex-col text-small">
                    <div class="flex justify-between gap-4 border-b border-line py-2">
                        <dt class="text-muted">ID</dt>
                        <dd class="m-0 tabular-nums">{{ $user->id }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-line py-2">
                        <dt class="text-muted">Registriert</dt>
                        <dd class="m-0 tabular-nums">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-line py-2">
                        <dt class="text-muted">Zuletzt aktiv</dt>
                        <dd class="m-0 tabular-nums">{{ $user->updated_at->format('d.m.Y H:i') }}</dd>
                    </div>
                    @if ($user->isSsoUser())
                        <div class="flex justify-between gap-4 border-b border-line py-2">
                            <dt class="text-muted">SSO</dt>
                            <dd class="m-0">{{ $user->ssoProviderName() }}</dd>
                        </div>
                    @endif
                </dl>
            </section>
        </div>
    </div>
</x-layouts.app>
