@props(['booking'])

{{-- Fortschrittsanzeige des Buchungsablaufs (mobil: untereinander, ab sm: nebeneinander) --}}
@if ($booking->status === 'cancelled')
    @php
        $cancelledTitle =
            'Storniert' .
            ($booking->cancelled_at ? ' am ' . $booking->cancelled_at->format('d.m.Y') : '') .
            match ($booking->cancelled_by) {
                'customer' => ' · durch den Kunden',
                'organizer' => ' · durch den Veranstalter',
                'event' => ' · Veranstaltung abgesagt',
                'system' => ' · automatisch (Zahlung nicht abgeschlossen)',
                default => '',
            };
    @endphp
    <x-ui.alert tone="danger" {{ $attributes }} :title="$cancelledTitle">
        @if ($booking->cancellation_reason)
            <p class="mt-1">Grund: {{ $booking->cancellation_reason }}</p>
        @endif
    </x-ui.alert>
@else
    @php $steps = $booking->progressSteps(); @endphp
    <ol {{ $attributes->class('m-0 flex list-none flex-col gap-3 p-0 sm:flex-row sm:items-start sm:gap-0') }}
        aria-label="Buchungsfortschritt">
        @foreach ($steps as $step)
            <li class="relative flex items-center gap-3 sm:flex-1 sm:flex-col sm:items-start sm:gap-2 sm:pr-4"
                @if ($step['state'] === 'current') aria-current="step" @endif>
                {{-- Verbindungslinie zum nächsten Schritt (nur ab sm) --}}
                @unless ($loop->last)
                    <span aria-hidden="true" @class([
                        'absolute top-3.5 left-9 hidden h-px right-2 sm:block',
                        'bg-primary' => $step['state'] === 'done',
                        'bg-line' => $step['state'] !== 'done',
                    ])></span>
                @endunless
                <span @class([
                    'relative flex h-7 w-7 flex-none items-center justify-center rounded-full text-small font-semibold tabular-nums',
                    'bg-primary text-on-primary' => $step['state'] === 'done',
                    'border-2 border-ink bg-surface text-ink' => $step['state'] === 'current',
                    'border border-line bg-surface text-muted' => $step['state'] === 'upcoming',
                ])>
                    @if ($step['state'] === 'done')
                        <x-icon.check class="h-4 w-4" />
                        <span class="sr-only">erledigt:</span>
                    @else
                        {{ $loop->iteration }}
                    @endif
                </span>
                <span class="min-w-0">
                    <span @class([
                        'block text-small',
                        'font-medium' => $step['state'] !== 'current',
                        'text-ink' => $step['state'] !== 'upcoming',
                        'text-muted' => $step['state'] === 'upcoming',
                        'font-semibold' => $step['state'] === 'current',
                    ])>{{ $step['label'] }}</span>
                    @if ($step['hint'])
                        <span class="block text-caption text-muted tabular-nums">{{ $step['hint'] }}</span>
                    @endif
                </span>
            </li>
        @endforeach
    </ol>
@endif
