<x-layouts.app :title="'Organisation: ' . $organization->name" :header="false">
    <x-ui.page-header :title="$organization->name" :subtitle="$organization->email" :back-href="route('admin.organizations.index')" back-label="Zur Übersicht" />

    <div class="mb-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Veranstaltungen gesamt" :value="$stats['total_events']" />
        <x-ui.stat label="Veröffentlicht" :value="$stats['published_events']" />
        <x-ui.stat label="Kommende" :value="$stats['upcoming_events']" />
        <x-ui.stat label="Buchungen" :value="$stats['total_bookings']" />
    </div>

    <div class="mb-12 grid gap-x-12 gap-y-12 lg:grid-cols-2">
        <x-ui.section class="min-w-0" title="Grundinformationen" level="h2">
            <x-ui.dl>
                <x-ui.dl-item label="Name">{{ $organization->name }}</x-ui.dl-item>
                <x-ui.dl-item label="E-Mail">{{ $organization->email }}</x-ui.dl-item>
                <x-ui.dl-item label="Telefon">{{ $organization->phone ?? 'Nicht angegeben' }}</x-ui.dl-item>
                <x-ui.dl-item label="Website">
                    @if ($organization->website)
                        <a href="{{ $organization->website }}" target="_blank" rel="noopener"
                            class="font-medium text-ink underline underline-offset-[3px]">{{ $organization->website }}</a>
                    @else
                        Nicht angegeben
                    @endif
                </x-ui.dl-item>
                <x-ui.dl-item label="Status">
                    @if ($organization->is_active)
                        <x-ui.status tone="success">Aktiv</x-ui.status>
                    @else
                        <x-ui.status tone="neutral">Inaktiv</x-ui.status>
                    @endif
                </x-ui.dl-item>
                <x-ui.dl-item label="Erstellt am" num>{{ $organization->created_at->format('d.m.Y H:i') }}
                    Uhr</x-ui.dl-item>
            </x-ui.dl>
        </x-ui.section>

        <x-ui.section class="min-w-0" title="Gebühren-Einstellungen" level="h2" :link-href="route('admin.organizer-fees.edit', $organization)"
            link-label="Bearbeiten">
            @if ($organization->custom_platform_fee && ($organization->custom_platform_fee['enabled'] ?? false))
                @php
                    $customFee = $organization->custom_platform_fee;
                    $feeType = $customFee['fee_type'] ?? 'percentage';
                @endphp
                <p class="mb-4 mt-0">
                    <x-ui.status tone="info">Individuelle Gebühren aktiv</x-ui.status>
                </p>
                <x-ui.dl>
                    <x-ui.dl-item
                        label="Gebührentyp">{{ $feeType === 'percentage' ? 'Prozentual' : 'Festbetrag' }}</x-ui.dl-item>
                    <x-ui.dl-item label="Gebühr" num>
                        @if ($feeType === 'percentage')
                            {{ \App\Support\Ui::percent($customFee['fee_percentage'] ?? 0, 2) }}
                        @else
                            {{ number_format($customFee['fee_fixed_amount'] ?? 0, 2, ',', '.') }} €
                        @endif
                    </x-ui.dl-item>
                    <x-ui.dl-item label="Mindestgebühr"
                        num>{{ number_format($customFee['minimum_fee'] ?? 0, 2, ',', '.') }} €</x-ui.dl-item>
                    @if (!empty($customFee['notes']))
                        <x-ui.dl-item label="Notizen">{{ $customFee['notes'] }}</x-ui.dl-item>
                    @endif
                    @if (isset($customFee['updated_at']))
                        <x-ui.dl-item label="Letzte Aktualisierung"
                            num>{{ \Carbon\Carbon::parse($customFee['updated_at'])->format('d.m.Y H:i') }}
                            Uhr</x-ui.dl-item>
                    @endif
                </x-ui.dl>
            @else
                <p class="mb-4 mt-0 text-muted">Diese Organisation nutzt die Standard-Plattformgebühren.</p>
                <x-ui.button variant="secondary" :href="route('admin.organizer-fees.edit', $organization)">Individuelle Gebühren festlegen</x-ui.button>
            @endif
        </x-ui.section>
    </div>

    <x-ui.section title="Eigentümer und Team" class="mb-12">
        <x-ui.table label="Eigentümer und Team">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">E-Mail</th>
                    <th scope="col">Rolle</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">{{ $organization->owner->name }}</th>
                    <td>{{ $organization->owner->email }}</td>
                    <td>Eigentümer</td>
                </tr>
                @foreach ($organization->members->where('id', '!=', $organization->owner_id) as $member)
<tr>
                        <th scope="row">{{ $member->name }}</th>
                        <td>{{ $member->email }}</td>
                        <td>Mitglied</td>
                    </tr>
@endforeach
            </tbody>
        </x-ui.table>
        @if ($organization->members->where('id', '!=', $organization->owner_id)->isEmpty())
<p class="mb-0 mt-3 text-small text-muted">Keine weiteren Team-Mitglieder.</p>
@endif
    </x-ui.section>

    <x-ui.section title="Aktionen">
        <div class="flex flex-wrap items-center gap-3">
            <x-ui.button :href="route('admin.organizer-fees.edit', $organization)">Gebühren verwalten</x-ui.button>

            <form method="POST" action="{{ route('admin.organizations.toggle-active', $organization) }}">
                @csrf
                <x-ui.button type="submit" variant="secondary">{{ $organization->is_active ? 'Deaktivieren' : 'Aktivieren' }}</x-ui.button>
            </form>

            @if ($stats['total_events'] === 0)
<form method="POST" action="{{ route('admin.organizations.destroy', $organization) }}"
                    onsubmit="return confirm('Organisation wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="danger">Löschen</x-ui.button>
                </form>
 @endif
                    </div>
                    </x-ui.section>
                    </x-layouts.app>)
