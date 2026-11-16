<x-layouts.app title="Log-Benachrichtigungen" :header="false">
    <x-ui.page-header title="Log-Benachrichtigungen"
        subtitle="Verwalten Sie Benachrichtigungen für kritische System-Fehler" :back-href="route('admin.system-logs.index')"
        back-label="Zurück zu System-Logs" />

    <x-ui.alert tone="info" title="Über Log-Benachrichtigungen" class="mb-12">
        <p class="m-0 mb-2">Benutzer mit der Berechtigung „Meldungen zu kritischen Logs erhalten“ bekommen automatisch
            E-Mails bei kritischen Systemfehlern.</p>
        <p class="m-0 mb-2"><strong class="font-semibold">Kritische Schweregrade:</strong> Fehler, Kritisch, Alarm,
            Notfall</p>
        <p class="m-0"><strong class="font-semibold">Funktionen:</strong></p>
        <ul class="m-0 mt-1 list-disc pl-5">
            <li>Doppelte Meldungen werden 5 Minuten lang unterdrückt</li>
            <li>Gruppierung ähnlicher Fehler</li>
            <li>Benachrichtigung per E-Mail und im Portal</li>
            <li>Verarbeitung im Hintergrund (Warteschlange)</li>
        </ul>
    </x-ui.alert>

    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Aktive Empfänger" :value="$usersWithPermission->count()" />
        <x-ui.stat label="Kritische Schweregrade" value="4" sub="Fehler, Kritisch, Alarm, Notfall" />
        <x-ui.stat label="Zeitfenster für Doppelte" value="5 Min" sub="Verhindert wiederholte Meldungen" />
    </div>

    <x-ui.section title="Test-Benachrichtigung" class="mb-12">
        <p class="m-0 mb-4 text-small text-muted">Senden Sie eine Test-Benachrichtigung an Ihre E-Mail-Adresse, um das
            System zu testen.</p>
        <form action="{{ route('admin.log-notifications.test') }}" method="POST">
            @csrf
            <x-ui.button type="submit" variant="secondary">
                <x-slot:icon><x-icon.mail /></x-slot:icon>
                Test-Benachrichtigung senden
            </x-ui.button>
        </form>
    </x-ui.section>

    <x-ui.section :title="'Benutzer mit Benachrichtigungen (' . $usersWithPermission->count() . ')'" class="mb-12">
        @if ($usersWithPermission->isEmpty())
            <x-ui.empty>Keine Benutzer haben derzeit die Berechtigung für Log-Benachrichtigungen.</x-ui.empty>
        @else
            <x-ui.table label="Benutzer mit Benachrichtigungen">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col" class="max-sm:hidden">E-Mail</th>
                        <th scope="col" class="max-sm:hidden">Rolle</th>
                        <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usersWithPermission as $user)
                        <tr>
                            <th scope="row">{{ $user->fullName() }}</th>
                            <td class="max-sm:hidden">{{ $user->email }}</td>
                            <td class="max-sm:hidden">
                                <x-ui.status
                                    tone="neutral">{{ \App\Support\AccessLabels::role($user->hasRole('admin') ? 'admin' : 'user') }}</x-ui.status>
                            </td>
                            <td class="actions">
                                <x-ui.button variant="secondary" size="sm" type="submit"
                                    form="revoke-{{ $user->id }}">Deaktivieren</x-ui.button>
                                <form id="revoke-{{ $user->id }}"
                                    action="{{ route('admin.log-notifications.revoke') }}" method="POST" class="hidden"
                                    onsubmit="return confirm('Benachrichtigungen für {{ $user->fullName() }} deaktivieren?')">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </x-ui.table>
        @endif
    </x-ui.section>

    <x-ui.section title="Benutzer hinzufügen">
        <form action="{{ route('admin.log-notifications.give') }}" method="POST"
            class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] items-end gap-x-4 gap-y-5">
            @csrf
            <x-ui.select label="Wählen Sie einen Administrator" name="user_id" required>
                <option value="">Benutzer auswählen</option>
                @foreach ($allAdmins as $admin)
                    @if (!$admin->hasPermissionTo('receive-critical-log-notifications'))
                        <option value="{{ $admin->id }}">{{ $admin->fullName() }} ({{ $admin->email }})</option>
                    @endif
                @endforeach
            </x-ui.select>
            <div>
                <x-ui.button type="submit" size="lg">
                    <x-slot:icon><x-icon.plus /></x-slot:icon>
                    Hinzufügen
                </x-ui.button>
            </div>
        </form>
    </x-ui.section>
</x-layouts.app>
