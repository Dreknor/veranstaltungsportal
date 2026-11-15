<x-layouts.public :title="'Buchung ' . $booking->booking_number">
    <div class="mx-auto max-w-page px-page pt-4 pb-[clamp(56px,7vw,96px)]">
        <div class="max-w-form">
            <x-ui.page-header title="Buchung verifizieren" :back-href="route('events.index')"
                back-label="Zur Übersicht">
                <x-slot:meta>
                    <span class="tabular-nums">Buchungsnummer: {{ $booking->booking_number }}</span>
                </x-slot:meta>
            </x-ui.page-header>

            <p class="mt-0 mb-6 text-base text-ink-soft">
                Bitte geben Sie Ihre E-Mail-Adresse ein, um auf die Buchungsdetails zuzugreifen.
            </p>

            @if ($errors->any() && !$errors->has('email'))
                <x-ui.alert tone="danger" class="mb-6">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <form action="{{ route('bookings.verify-email', $booking->booking_number) }}" method="POST"
                data-recaptcha data-recaptcha-action="booking_verify" class="flex flex-col gap-5">
                @csrf
                <x-ui.field label="E-Mail-Adresse" name="email" type="email" required autocomplete="email"
                    placeholder="name@beispiel.de"
                    hint="Verwenden Sie die E-Mail-Adresse, die Sie bei der Buchung angegeben haben." />

                <x-recaptcha action="booking_verify" />

                <div>
                    <x-ui.button type="submit" size="lg">Buchung ansehen</x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.public>
