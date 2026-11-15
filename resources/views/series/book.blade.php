@php
    use App\Support\Ui;

    $layout = auth()->check() ? 'layouts.app' : 'layouts.public';
    $first = $series->events->first();
    $count = $series->events->count();
@endphp
<x-dynamic-component :component="$layout" title="{{ $series->title }}">
    <div @class([
        'mx-auto max-w-page px-page pt-4 pb-[clamp(56px,7vw,96px)]' => !auth()->check(),
    ])>
        <x-ui.page-header title="Veranstaltungsreihe buchen" :back-href="route('series.show', $series)"
            back-label="Zurück zur Übersicht" />

        <div class="grid items-start gap-10 lg:grid-cols-[minmax(0,640px)_minmax(0,368px)] lg:justify-between lg:gap-x-12">
            <div class="min-w-0">
                {{-- Gebuchte Reihe --}}
                <x-ui.box tone="zone" padding="sm" class="flex flex-col gap-1">
                    <span class="text-small text-muted">Veranstaltungsreihe{{ $series->category ? ' · ' . $series->category->name : '' }}</span>
                    <span class="text-h3">{{ $series->title }}</span>
                    <span class="flex flex-wrap gap-x-4 gap-y-0.5 text-small text-ink-soft tabular-nums">
                        <span>{{ $count }} Termine</span>
                        @if ($first)
                            <span>ab {{ Ui::date($first->start_date) }}</span>
                            <span>{{ Ui::time($first->start_date, $first->end_date) }}</span>
                            @if (!empty($series->template_data['venue_city']))
                                <span>{{ $series->template_data['venue_city'] }}</span>
                            @endif
                        @endif
                    </span>
                </x-ui.box>

                <x-ui.alert tone="info" title="Wichtig" class="mt-6">
                    <p>
                        Mit dieser Buchung melden Sie sich für alle {{ $count }} Termine an.
                        Die Teilnahme an allen Terminen ist verbindlich. Einzelne Termine können nicht separat gebucht
                        oder storniert werden.
                    </p>
                </x-ui.alert>

                <form method="POST" action="{{ route('series.book', $series) }}" id="series-booking-form" class="m-0">
                    @csrf

                    <section aria-labelledby="schritt-1" class="py-8">
                        <x-ui.step-heading number="1" id="schritt-1" aside="* Pflichtfeld" class="mb-6">Persönliche
                            Daten</x-ui.step-heading>
                        <div class="flex flex-col gap-5">
                            <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                                <x-ui.field label="Vorname" name="first_name" :value="auth()->user()->first_name ?? ''"
                                    required autocomplete="given-name" />
                                <x-ui.field label="Nachname" name="last_name" :value="auth()->user()->last_name ?? ''"
                                    required autocomplete="family-name" />
                            </div>
                            <x-ui.field label="E-Mail" name="email" type="email" :value="auth()->user()->email ?? ''"
                                required autocomplete="email" />
                            <x-ui.field label="Telefon" name="phone" type="tel" :value="auth()->user()->phone ?? ''"
                                optional autocomplete="tel" />
                            <x-ui.textarea label="Anmerkungen" name="notes" rows="3" optional
                                hint="Haben Sie besondere Wünsche oder Fragen? Teilen Sie uns diese hier mit." />
                        </div>
                    </section>

                    <section aria-labelledby="schritt-2" class="border-t border-line pt-8">
                        <x-ui.step-heading number="2" id="schritt-2" class="mb-4">Bedingungen</x-ui.step-heading>
                        <x-ui.checkbox name="terms" required
                            hint="Ich bestätige, dass ich an allen {{ $count }} Terminen teilnehmen werde und die Teilnahmebedingungen akzeptiere.">
                            Ich akzeptiere die <a href="{{ route('agb') }}" target="_blank"
                                class="font-medium underline underline-offset-[3px]">AGB</a> und Datenschutzerklärung
                        </x-ui.checkbox>
                    </section>
                </form>
            </div>

            <x-ui.summary title="Alle Termine">
                <x-slot:event>
                    <span>{{ $series->title }}</span>
                    <span>{{ $count }} Termine</span>
                </x-slot:event>
                <ol class="m-0 flex list-none flex-col gap-2 border-t border-line p-0 pt-4">
                    @foreach ($series->events->sortBy('series_position') as $event)
                        <li class="flex justify-between gap-4 text-ui tabular-nums">
                            <span class="min-w-0">{{ $event->series_position }}. {{ Ui::date($event->start_date) }}</span>
                            <span class="whitespace-nowrap text-muted">{{ Ui::time($event->start_date, $event->end_date) }}</span>
                        </li>
                    @endforeach
                </ol>
                <div class="flex flex-col gap-3">
                    <x-ui.button type="submit" form="series-booking-form" size="xl" full>
                        Verbindlich buchen ({{ $count }} Termine)
                    </x-ui.button>
                    <x-ui.button variant="secondary" :href="route('series.show', $series)" full>Abbrechen</x-ui.button>
                </div>
            </x-ui.summary>
        </div>

        @if (isset($series->template_data['venue_name']))
            <x-ui.section title="Veranstaltungsort" class="mt-12 max-w-form">
                <address class="flex items-start gap-3 text-base not-italic">
                    <x-icon.location class="mt-1 h-5 w-5 flex-none text-muted" />
                    <span>
                        <span class="block font-semibold">{{ $series->template_data['venue_name'] }}</span>
                        <span class="block text-ink-soft">
                            {{ $series->template_data['venue_address'] }}<br>
                            {{ $series->template_data['venue_postal_code'] }} {{ $series->template_data['venue_city'] }}<br>
                            {{ $series->template_data['venue_country'] }}
                        </span>
                    </span>
                </address>
            </x-ui.section>
        @endif
    </div>
</x-dynamic-component>
