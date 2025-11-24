@if(auth()->check())
    <x-layouts.app>
        {{-- Für eingeloggte Benutzer: Nutzung des App-Layouts mit Sidebar/Header --}}
        @include('bookings.partials.details')
    </x-layouts.app>
@else
    {{-- Für Gäste bleibt das öffentliche Layout inkl. öffentlicher Navigation --}}
    <x-layouts.public :title="'Buchung ' . $booking->booking_number">
        <div class="min-h-screen bg-gray-50 py-8">
            @include('bookings.partials.details')
        </div>
    </x-layouts.public>
@endif

