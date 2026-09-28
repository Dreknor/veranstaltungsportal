@props(['booking'])

{{-- Fortschrittsanzeige des Buchungsablaufs (mobil: vertikal, ab sm: horizontal) --}}
@if($booking->status === 'cancelled')
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-4']) }}>
        <p class="font-semibold text-red-800 dark:text-red-200">
            Storniert{{ $booking->cancelled_at ? ' am ' . $booking->cancelled_at->format('d.m.Y') : '' }}
            @switch($booking->cancelled_by)
                @case('customer') · durch den Kunden @break
                @case('organizer') · durch den Veranstalter @break
                @case('event') · Veranstaltung abgesagt @break
                @case('system') · automatisch (Zahlung nicht abgeschlossen) @break
            @endswitch
        </p>
        @if($booking->cancellation_reason)
            <p class="text-sm text-red-700 dark:text-red-300 mt-1">Grund: {{ $booking->cancellation_reason }}</p>
        @endif
    </div>
@else
    @php $steps = $booking->progressSteps(); @endphp
    <ol {{ $attributes->merge(['class' => 'flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-0']) }} aria-label="Buchungsfortschritt">
        @foreach($steps as $step)
            <li class="flex sm:flex-col sm:flex-1 items-center sm:items-center gap-3 sm:gap-2 relative sm:text-center"
                @if($step['state'] === 'current') aria-current="step" @endif>
                {{-- Verbindungslinie (nur Desktop) --}}
                @unless($loop->first)
                    <span class="hidden sm:block absolute top-4 right-1/2 w-full h-0.5 -z-0
                                 {{ $step['state'] === 'upcoming' ? 'bg-gray-200 dark:bg-gray-700' : 'bg-green-400' }}" aria-hidden="true"></span>
                @endunless
                <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold
                    @if($step['state'] === 'done') bg-green-500 text-white
                    @elseif($step['state'] === 'current') bg-amber-400 text-amber-950 ring-4 ring-amber-100 dark:ring-amber-900/50
                    @else bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 @endif">
                    @if($step['state'] === 'done')
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span class="sr-only">erledigt:</span>
                    @else
                        {{ $loop->iteration }}
                    @endif
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-medium
                        {{ $step['state'] === 'upcoming' ? 'text-gray-500 dark:text-gray-400' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ $step['label'] }}
                    </span>
                    @if($step['hint'])
                        <span class="block text-xs text-gray-500 dark:text-gray-400">{{ $step['hint'] }}</span>
                    @endif
                </span>
            </li>
        @endforeach
    </ol>
@endif
