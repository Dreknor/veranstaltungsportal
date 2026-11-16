<x-layouts.app title="Benutzer-Report" :header="false">
    <x-ui.page-header title="Benutzer-Report" subtitle="Aktivität, Rollen und aktivste Benutzer"
        :back-href="route('admin.reporting.index', ['period' => $period])" back-label="Zurück zur Übersicht" />

    <form method="GET" class="mb-8 flex flex-wrap items-end gap-x-4 gap-y-3">
        <x-ui.select label="Zeitraum" name="period" :old="false" onchange="this.form.submit()" class="w-full max-w-64">
            <option value="7days" @selected($period === '7days')>Letzte 7 Tage</option>
            <option value="30days" @selected($period === '30days')>Letzte 30 Tage</option>
            <option value="90days" @selected($period === '90days')>Letzte 90 Tage</option>
            <option value="365days" @selected($period === '365days')>Letztes Jahr</option>
            <option value="all" @selected($period === 'all')>Gesamt</option>
        </x-ui.select>
        <x-ui.button type="submit" variant="secondary" size="lg">Anzeigen</x-ui.button>
    </form>

    <div class="mb-12 grid grid-cols-1 gap-x-12 gap-y-12 lg:grid-cols-2">
        <x-ui.section title="Benutzeraktivität">
            <x-ui.dl>
                <x-ui.dl-item label="Aktive Benutzer" num>{{ number_format($data['user_activity']['active_users']) }}</x-ui.dl-item>
                <x-ui.dl-item label="Inaktive Benutzer" num>{{ number_format($data['user_activity']['inactive_users']) }}</x-ui.dl-item>
            </x-ui.dl>
        </x-ui.section>

        <x-ui.section title="Benutzerrollen">
            <x-ui.dl>
                @foreach ($data['user_roles'] as $role)
                    <x-ui.dl-item :label="$role->is_organizer ? 'Veranstalter' : 'Teilnehmer'" num>{{ number_format($role->count) }}</x-ui.dl-item>
                @endforeach
            </x-ui.dl>
        </x-ui.section>
    </div>

    <x-ui.section title="Top 10 Benutzer (nach Buchungen)">
        <x-ui.table label="Top 10 Benutzer nach Buchungen">
            <thead>
                <tr>
                    <th scope="col">Benutzer</th>
                    <th scope="col">E-Mail</th>
                    <th scope="col">Rolle</th>
                    <th scope="col" class="num">Buchungen</th>
                    <th scope="col">Registriert</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['top_users'] as $user)
                    <tr>
                        <th scope="row">{{ $user->name }}</th>
                        <td>{{ $user->email }}</td>
                        <td><x-ui.status tone="neutral">{{ $user->is_organizer ? 'Veranstalter' : 'Teilnehmer' }}</x-ui.status></td>
                        <td class="num">{{ $user->bookings_count }}</td>
                        <td class="tabular-nums whitespace-nowrap">{{ $user->created_at->format('d.m.Y') }}</td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="5" class="p-0">
                            <x-ui.empty :bordered="false">Keine Daten verfügbar</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </x-ui.section>
</x-layouts.app>
