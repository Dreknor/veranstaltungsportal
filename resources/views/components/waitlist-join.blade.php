{{--
    Warteliste einer ausgebuchten Veranstaltung. Der Block trägt id="warteliste" – die Listenzeilen
    (<x-ui.event-row>) und der Kasten der Detailseite springen dorthin.
--}}
@props(['event'])

@php
    $isSoldOut = $event->ticketTypes->every(fn($ticket) => $ticket->availableQuantity() === 0);
    $userOnWaitlist = false;

    if (auth()->check()) {
        $userOnWaitlist = \App\Models\EventWaitlist::where('event_id', $event->id)
            ->where('email', auth()->user()->email)
            ->whereIn('status', ['waiting', 'notified'])
            ->exists();
    }
@endphp

@if ($isSoldOut)
    <section id="warteliste" aria-labelledby="warteliste-titel" {{ $attributes->class('scroll-mt-6') }}>
        <h2 id="warteliste-titel" class="m-0 mb-4 text-h2">Warteliste</h2>

        @if ($userOnWaitlist)
            <x-ui.alert tone="success" title="Sie stehen bereits auf der Warteliste.">
                Wir benachrichtigen Sie per E-Mail, sobald ein Platz frei wird.
            </x-ui.alert>

            <form method="POST" action="{{ route('waitlist.leave', $event) }}" class="mt-4">
                @csrf
                <x-ui.button type="submit" variant="secondary">Von der Warteliste entfernen</x-ui.button>
            </form>
        @else
            <p class="m-0 mb-6 max-w-prose text-base text-ink-soft">
                Alle Plätze dieser Veranstaltung sind vergeben. Tragen Sie sich in die Warteliste ein – wir
                benachrichtigen Sie per E-Mail, sobald ein Platz frei wird. Sie haben dann 48 Stunden Zeit, um zu
                buchen.
            </p>

            <form method="POST" action="{{ route('waitlist.join', $event) }}" class="flex max-w-form flex-col gap-y-5"
                data-recaptcha data-recaptcha-action="waitlist">
                @csrf

                <p class="m-0 text-small text-muted">* Pflichtfeld</p>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Name" name="name" id="waitlist_name" required :value="auth()->check() ? auth()->user()->fullName() : null"
                        autocomplete="name" />
                    <x-ui.field label="E-Mail" name="email" id="waitlist_email" type="email" required
                        :value="auth()->check() ? auth()->user()->email : null" autocomplete="email" />
                    <x-ui.field label="Telefon" name="phone" id="waitlist_phone" type="tel" optional
                        :value="auth()->check() ? auth()->user()->phone : null" autocomplete="tel" />
                    <x-ui.select label="Anzahl Plätze" name="quantity" id="waitlist_quantity" required
                        :options="array_combine(range(1, 10), range(1, 10))" />
                </div>

                @if ($event->ticketTypes->count() > 1)
                    <x-ui.select label="Ticketart" name="ticket_type_id" id="waitlist_ticket_type" optional
                        placeholder="Beliebig">
                        @foreach ($event->ticketTypes as $ticketType)
                            <option value="{{ $ticketType->id }}" @selected((string) old('ticket_type_id') === (string) $ticketType->id)>
                                {{ $ticketType->name }} ({{ \App\Support\Ui::money($ticketType->price) }})</option>
                        @endforeach
                    </x-ui.select>
                @endif

                <!-- reCAPTCHA -->
                <x-recaptcha action="waitlist" />
                <x-ui.field-error name="recaptcha" id="waitlist-recaptcha-error" />
                <x-ui.field-error name="event" id="waitlist-event-error" />

                <div>
                    <x-ui.button type="submit">Auf die Warteliste setzen</x-ui.button>
                </div>
            </form>
        @endif
    </section>
@endif
