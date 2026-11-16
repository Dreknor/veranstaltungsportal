<x-layouts.app title="Log-Benachrichtigungen" :header="false">
    <x-ui.page-header title="Log-Benachrichtigungen" subtitle="Verwalten Sie Benachrichtigungen für kritische System-Fehler"
        :back-href="route('admin.system-logs.index')" back-label="Zurück zu System-Logs" />

    <x-ui.alert tone="info" title="Über Log-Benachrichtigungen" class="mb-12">
        <p class="m-0 mb-2">Benutzer mit der Permission "receive-critical-log-notifications" erhalten automatisch E-Mail-Benachrichtigungen bei kritischen System-Fehlern.</p>
        <p class="m-0 mb-2"><strong class="font-semibold">Kritische Log-Levels:</strong> ERROR, CRITICAL, ALERT, EMERGENCY</p>
        <p class="m-0"><strong class="font-semibold">Features:</strong></p>
        <ul class="m-0 mt-1 list-disc pl-5">
            <li>Automatische Duplicate-Detection (5 Minuten)</li>
            <li>Gruppierung ähnlicher Fehler</li>
            <li>E-Mail und Datenbank-Benachrichtigungen</li>
            <li>Asynchrone Verarbeitung via Queue</li>
        </ul>
    </x-ui.alert>

    <div class="mb-12 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Aktive Empfänger" :value="$usersWithPermission->count()" />
        <x-ui.stat label="Kritische Levels" value="4" sub="ERROR, CRITICAL, ALERT, EMERGENCY" />
        <x-ui.stat label="Duplicate-Fenster" value="5 Min" sub="Verhindert Spam-Benachrichtigungen" />
    </div>

    <x-ui.section title="Test-Benachrichtigung" class="mb-12">
        <p class="m-0 mb-4 text-small text-muted">Senden Sie eine Test-Benachrichtigung an Ihre E-Mail-Adresse, um das System zu testen.</p>
        <form action="{{ route('admin.log-notifications.test') }}" method="POST">
            @csrf
            <x-ui.button type="submit" variant="secondary">
                <x-slot:icon><x-icon.mail /></x-slot:icon>
                Test-Benachrichtigung senden
            </x-ui.button>
        </form>
    </x-ui.section>

    <x-ui.section title="Benutzer mit Benachrichtigungen ({{ $usersWithPermission->count() }})" class="mb-12">
        @if ($usersWithPermission->isEmpty())
            <x-ui.empty>Keine Benutzer haben derzeit die Berechtigung für Log-Benachrichtigungen.</x-ui.empty>
        @else
            <x-ui.table label="Benutzer mit Benachrichtigungen">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">E-Mail</th>
                        <th scope="col">Rolle</th>
                        <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usersWithPermission as $user)
                        <tr>
                            <th scope="row">{{ $user->name }}</th>
                            <td>{{ $user->email }}</td>
                            <td>
                                <x-ui.status tone="neutral">{{ $user->hasRole('admin') ? 'Admin' : 'Benutzer' }}</x-ui.status>
                            </td>
                            <td class="actions">
                                <x-ui.button variant="secondary" size="sm" type="submit" form="revoke-{{ $user->id }}">Deaktivieren</x-ui.button>
                                <form id="revoke-{{ $user->id }}" action="{{ route('admin.log-notifications.revoke') }}" method="POST" class="hidden"
                                    onsubmit="return confirm('Benachrichtigungen für {{ $user->name }} deaktivieren?')">
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
                        <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->email }})</option>
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
