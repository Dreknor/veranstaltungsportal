@if (auth()->check())
    {{-- Für eingeloggte Benutzer: App-Layout mit Seitenleiste (bringt Seitenbreite und Abstände mit) --}}
    <x-layouts.app :title="'Buchung ' . $booking->booking_number">
        @include('bookings.partials.details')
    </x-layouts.app>
@else
    {{-- Für Gäste bleibt das öffentliche Layout inkl. öffentlicher Navigation --}}
    <x-layouts.public :title="'Buchung ' . $booking->booking_number">
        <div class="mx-auto max-w-page px-page pt-8 pb-[clamp(56px,7vw,96px)]">
            @include('bookings.partials.details')
        </div>
    </x-layouts.public>
@endif
