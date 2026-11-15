<x-layouts.app title="Teilnehmende kontaktieren">
    <x-ui.page-header title="Teilnehmende kontaktieren" :back-href="route('organizer.events.edit', $event)" back-label="Zur Veranstaltung"
        subtitle="{{ $event->title }} · {{ \App\Support\Ui::date($event->start_date) }}, {{ $event->start_date->format('H:i') }} Uhr" />

    <form method="POST" action="{{ route('organizer.events.attendees.contact.send', $event) }}" class="flex max-w-form flex-col gap-8"
        x-data="{ segment: '{{ old('segment', 'all') }}', counts: @js($segments->map(fn($s) => $s['count'])) }">
        @csrf

        <p class="m-0 text-small text-muted">* Pflichtfeld</p>

        <fieldset class="m-0 min-w-0 border-0 p-0">
            <x-ui.label as="legend" class="mb-2">Empfänger</x-ui.label>
            <div class="flex flex-col gap-3">
                @foreach ($segments as $key => $segment)
                    <x-ui.choice name="segment" :value="$key" :title="$segment['label']" :checked="$key === 'all'" x-model="segment"
                        @class(['opacity-60' => $segment['count'] === 0])>
                        <x-slot:aside>{{ $segment['count'] }}</x-slot:aside>
                    </x-ui.choice>
                @endforeach
            </div>
            <input type="hidden" name="include_attendees" value="0">
            <x-ui.checkbox name="include_attendees" :checked="true" class="mt-3"
                label="Auch eingetragene Teilnehmende mit eigener E-Mail-Adresse anschreiben (z. B. Kolleg:innen, für die gebucht wurde)" />
        </fieldset>

        <div class="flex flex-col gap-5">
            <x-ui.field label="Betreff" name="subject" required maxlength="255" placeholder="z. B. Raumänderung für morgen" />

            <x-ui.textarea label="Nachricht" name="message" required rows="10" maxlength="5000" placeholder="Ihre Nachricht …"
                hint="Die Anrede („Hallo …“), Veranstaltungsdaten und ein Link zur Buchung werden automatisch ergänzt. Antworten gehen direkt an die E-Mail-Adresse Ihrer Organisation. Für Änderungen an Termin, Ort oder Online-Link ist keine Nachricht nötig – darüber informiert das Portal automatisch." />
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-line pt-6 sm:flex-row sm:justify-end">
            <x-ui.button variant="secondary" :href="route('organizer.events.edit', $event)">Abbrechen</x-ui.button>
            <x-ui.button type="submit" x-bind:disabled="counts[segment] === 0"
                @click="if (!confirm('Nachricht an ' + counts[segment] + ' Buchung(en) senden?')) $event.preventDefault()">
                Nachricht senden (<span x-text="counts[segment]">{{ $attendeesCount }}</span> Buchungen)
            </x-ui.button>
        </div>
    </form>
</x-layouts.app>
