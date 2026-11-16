<x-layouts.public :title="'Tickets personalisieren - ' . $booking->event->title">
    <div class="mx-auto max-w-page px-page pt-4 pb-[clamp(56px,7vw,96px)]">
        <div class="max-w-form">
            <x-ui.page-header title="Tickets personalisieren" :subtitle="$booking->event->title"
                :back-href="route('bookings.show', $booking->booking_number)" back-label="Zurück zur Buchung" />

            <x-ui.alert tone="info" title="Wichtig: Personalisierung erforderlich" class="mb-8">
                <p>
                    Sie haben mehrere Tickets gebucht. Bitte geben Sie für jedes Ticket die Daten des jeweiligen
                    Teilnehmers an. Die Tickets werden erst nach der Personalisierung per E-Mail versendet.
                </p>
            </x-ui.alert>

            <form method="POST" action="{{ route('bookings.save-personalization', $booking->booking_number) }}">
                @csrf

                <div class="mb-2 flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2">
                    <h2 class="m-0 text-h2-form">Teilnehmerdaten</h2>
                    <span class="text-small text-muted">* Pflichtfeld</span>
                </div>

                @foreach ($booking->items as $index => $item)
                    <fieldset class="m-0 min-w-0 border-0 border-b border-line px-0 py-6">
                        <legend class="float-left mb-4 w-full p-0 text-h3">
                            Ticket {{ $index + 1 }}: {{ $item->ticketType->name }}
                        </legend>
                        <div class="clear-left grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                            <x-ui.field label="Name des Teilnehmers" id="attendee_name_{{ $item->id }}"
                                name="attendees[{{ $item->id }}][attendee_name]"
                                :value="$item->attendee_name ?? ($index === 0 ? $booking->customer_name : '')" required
                                autocomplete="off" placeholder="Vor- und Nachname" />
                            <x-ui.field label="E-Mail des Teilnehmers" type="email" id="attendee_email_{{ $item->id }}"
                                name="attendees[{{ $item->id }}][attendee_email]"
                                :value="$item->attendee_email ?? ($index === 0 ? $booking->customer_email : '')" required
                                autocomplete="off" placeholder="email@beispiel.de" />
                            @if ($booking->event->showsOrganizationField())
                                <x-ui.field label="Organisation / Einrichtung" id="attendee_organization_{{ $item->id }}"
                                    name="attendees[{{ $item->id }}][attendee_organization]"
                                    :value="$item->attendee_organization ?? ($index === 0 ? $booking->customer_organization : '')"
                                    :required="$booking->event->requiresOrganizationField()"
                                    :optional="!$booking->event->requiresOrganizationField()"
                                    placeholder="z. B. Evangelische Grundschule Dresden" />
                            @endif
                        </div>
                    </fieldset>
                @endforeach

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-ui.button type="submit" wrap>Personalisierung speichern und Tickets
                        versenden</x-ui.button>
                    <x-ui.button variant="secondary" :href="route('bookings.show', $booking->booking_number)">
                        Abbrechen
                    </x-ui.button>
                </div>
            </form>

            <section aria-labelledby="hinweise" class="mt-12 border-t border-line pt-6">
                <h2 id="hinweise" class="m-0 mb-2 text-h3">Hinweise</h2>
                <ul class="m-0 flex list-disc flex-col gap-1 pl-5 text-base text-ink-soft">
                    <li>Alle Felder sind Pflichtfelder.</li>
                    <li>Die Tickets werden nach der Personalisierung automatisch an die E-Mail-Adresse des Käufers
                        versendet.</li>
                    <li>Jedes Ticket wird auf den Namen des jeweiligen Teilnehmers ausgestellt.</li>
                    <li>Bei Fragen wenden Sie sich bitte an den Veranstalter.</li>
                </ul>
            </section>
        </div>
    </div>
</x-layouts.public>
