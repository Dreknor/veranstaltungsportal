<x-layouts.app title="Rabattcodes für {{ $event->title }}">
    <x-ui.page-header title="Rabattcodes" :subtitle="$event->title" :back-href="route('organizer.events.edit', $event)" back-label="Zurück zur Veranstaltung">
        <x-slot:actions>
            <x-ui.button :href="route('organizer.events.discount-codes.create', $event)">
                <x-slot:icon><x-icon.plus /></x-slot:icon>
                Neuer Rabattcode
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($discountCodes->count() > 0)
        <x-ui.table label="Rabattcodes">
            <thead>
                <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Typ</th>
                    <th scope="col" class="num">Wert</th>
                    <th scope="col" class="num">Verwendet</th>
                    <th scope="col">Gültig</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($discountCodes as $code)
                    <tr>
                        <th scope="row" class="tabular-nums">{{ $code->code }}</th>
                        <td>{{ $code->type === 'percentage' ? 'Prozent' : 'Festbetrag' }}</td>
                        <td class="num">
                            @if ($code->type === 'percentage')
                                {{ rtrim(rtrim(number_format($code->value, 2, ',', '.'), '0'), ',') }} %
                            @else
                                {{ \App\Support\Ui::money($code->value) }}
                            @endif
                        </td>
                        <td class="num">
                            {{ $code->usage_count }}@if ($code->usage_limit)
                                / {{ $code->usage_limit }}
                            @endif
                        </td>
                        <td class="tabular-nums whitespace-nowrap">
                            @if ($code->valid_from || $code->valid_until)
                                @if ($code->valid_from)
                                    <span class="block">Ab: {{ $code->valid_from->format('d.m.Y') }}</span>
                                @endif
                                @if ($code->valid_until)
                                    <span class="block">Bis: {{ $code->valid_until->format('d.m.Y') }}</span>
                                @endif
                            @else
                                <span class="text-muted">Unbegrenzt</span>
                            @endif
                        </td>
                        <td>
                            @if ($code->is_active && $code->isValid())
                                <x-ui.status tone="success">Aktiv</x-ui.status>
                            @elseif ($code->is_active && !$code->isValid())
                                <x-ui.status tone="warning">Abgelaufen</x-ui.status>
                            @else
                                <x-ui.status tone="neutral">Inaktiv</x-ui.status>
                            @endif
                        </td>
                        <td class="actions">
                            <x-ui.button size="sm" variant="secondary" :href="route('organizer.events.discount-codes.edit', [$event, $code])">Bearbeiten</x-ui.button>
                            <x-ui.dropdown label="Weitere Aktionen für {{ $code->code }}" icon-only>
                                <x-ui.dropdown-item type="submit"
                                    form="code-toggle-{{ $code->id }}">{{ $code->is_active ? 'Deaktivieren' : 'Aktivieren' }}</x-ui.dropdown-item>
                                @if ($code->bookings_count == 0)
                                    <x-ui.dropdown-item type="submit" form="code-delete-{{ $code->id }}"
                                        danger>Löschen</x-ui.dropdown-item>
                                @endif
                            </x-ui.dropdown>
                            <form id="code-toggle-{{ $code->id }}"
                                action="{{ route('organizer.events.discount-codes.toggle', [$event, $code]) }}"
                                method="POST" class="hidden">
                                @csrf
                                @method('PATCH')
                            </form>
                            @if ($code->bookings_count == 0)
                                <form id="code-delete-{{ $code->id }}"
                                    action="{{ route('organizer.events.discount-codes.destroy', [$event, $code]) }}"
                                    method="POST" class="hidden"
                                    onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Rabattcode löschen möchten?');">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </x-ui.table>
    @else
        <x-ui.empty title="Keine Rabattcodes" :action-href="route('organizer.events.discount-codes.create', $event)" action-label="Rabattcode erstellen"
            action-variant="primary">
            Erstellen Sie Ihren ersten Rabattcode für diese Veranstaltung.
        </x-ui.empty>
    @endif
</x-layouts.app>
