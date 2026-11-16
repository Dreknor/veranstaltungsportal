<x-layouts.app title="Organisationen verwalten" :header="false">
    <x-ui.page-header title="Organisationen" subtitle="Verwalten Sie alle Organisationen und deren Gebühren." />

    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-ui.stat label="Gesamt" :value="$stats['total']" />
        <x-ui.stat label="Aktiv" :value="$stats['active']" />
        <x-ui.stat label="Inaktiv" :value="$stats['inactive']" />
        <x-ui.stat label="Mit individuellen Gebühren" :value="$stats['with_custom_fees']" />
    </div>

    <form method="GET" action="{{ route('admin.organizations.index') }}"
        class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] items-end gap-x-4 gap-y-5">
        <x-ui.field label="Suche" name="search" :old="false" :value="request('search')" placeholder="Name oder E-Mail" />
        <x-ui.select label="Status" name="status" :old="false" :options="['' => 'Alle', 'active' => 'Aktiv', 'inactive' => 'Inaktiv']" :selected="request('status')" />
        <x-ui.select label="Individuelle Gebühren" name="custom_fees" :old="false" :options="['' => 'Alle', 'yes' => 'Ja', 'no' => 'Nein']"
            :selected="request('custom_fees')" />
        <div>
            <x-ui.button type="submit" variant="secondary" size="lg" full>Filtern</x-ui.button>
        </div>
    </form>

    <x-ui.table label="Organisationen">
        <thead>
            <tr>
                <th scope="col">Organisation</th>
                <th scope="col">Eigentümer</th>
                <th scope="col" class="num">Veranstaltungen</th>
                <th scope="col">Status</th>
                <th scope="col">Gebühren</th>
                <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($organizations as $organization)
                <tr>
                    <th scope="row">
                        <span class="flex items-center gap-3">
                            @if ($organization->logo_path)
                                <img src="{{ asset('storage/' . $organization->logo_path) }}" alt=""
                                    class="h-10 w-10 shrink-0 rounded-full border border-line object-cover">
                            @endif
                            <span>
                                <span class="block">{{ $organization->name }}</span>
                                <span class="block text-small font-normal text-muted">{{ $organization->email }}</span>
                            </span>
                        </span>
                    </th>
                    <td>
                        <span class="block">{{ $organization->owner->name ?? 'N/A' }}</span>
                        <span class="block text-small text-muted">{{ $organization->owner->email ?? 'N/A' }}</span>
                    </td>
                    <td class="num">{{ $organization->events_count }}</td>
                    <td>
                        @if ($organization->is_active)
                            <x-ui.status tone="success">Aktiv</x-ui.status>
                        @else
                            <x-ui.status tone="neutral">Inaktiv</x-ui.status>
                        @endif
                    </td>
                    <td class="whitespace-nowrap tabular-nums">
                        @if ($organization->custom_platform_fee && ($organization->custom_platform_fee['enabled'] ?? false))
                            @php
                                $customFee = $organization->custom_platform_fee;
                                $feeType = $customFee['fee_type'] ?? 'percentage';
                                $feeValue =
                                    $feeType === 'percentage'
                                        ? \App\Support\Ui::percent($customFee['fee_percentage'] ?? 0, 2)
                                        : number_format($customFee['fee_fixed_amount'] ?? 0, 2, ',', '.') . ' €';
                            @endphp
                            {{ $feeValue }}
                        @else
                            <span class="text-muted">Standard</span>
                        @endif
                    </td>
                    <td class="actions">
                        <x-ui.dropdown :label="'Aktionen für ' . $organization->name" icon-only>
                            <x-ui.dropdown-item :href="route('admin.organizations.show', $organization)">Details</x-ui.dropdown-item>
                            <x-ui.dropdown-item :href="route('admin.organizer-fees.edit', $organization)">Gebühren festlegen</x-ui.dropdown-item>
                            <x-ui.dropdown-item type="submit"
                                form="toggle-active-{{ $organization->id }}">{{ $organization->is_active ? 'Deaktivieren' : 'Aktivieren' }}</x-ui.dropdown-item>
                        </x-ui.dropdown>
                        <form id="toggle-active-{{ $organization->id }}" method="POST"
                            action="{{ route('admin.organizations.toggle-active', $organization) }}" class="hidden">
                            @csrf</form>
                    </td>
                </tr>
            @empty
                <tr class="hover:bg-transparent">
                    <td colspan="6" class="p-0">
                        <x-ui.empty :bordered="false" title="Keine Organisationen gefunden">
                            Versuchen Sie, Ihre Suchfilter anzupassen.
                        </x-ui.empty>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-ui.table>

    @if ($organizations->hasPages())
        <div class="mt-6">
            {{ $organizations->links() }}
        </div>
    @endif
</x-layouts.app>
