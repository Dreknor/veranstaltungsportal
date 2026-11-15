<x-layouts.app>
    <x-ui.page-header title="Einstellungen" class="mb-4" />

    @include('settings.partials.navigation')

    <div class="max-w-page">
        <!-- Newsletter Section -->
        <x-ui.section title="Newsletter-Abonnement" class="max-w-form">
            <form action="{{ route('newsletter.subscribe') }}" method="POST">
                @csrf

                <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 border-y border-line py-4">
                    <div class="min-w-0 flex-[1_1_240px]">
                        <p class="m-0 text-base font-medium text-ink">Newsletter erhalten</p>
                        <p class="m-0 mt-1 text-small text-muted">
                            Erhalten Sie regelmäßig Updates über neue Veranstaltungen
                        </p>
                        @if (auth()->user()->newsletter_subscribed && auth()->user()->newsletter_subscribed_at)
                            <p class="m-0 mt-1 text-small text-ink-soft tabular-nums">
                                Abonniert seit: {{ auth()->user()->newsletter_subscribed_at->format('d.m.Y') }}
                            </p>
                        @endif
                    </div>
                    @if (auth()->user()->newsletter_subscribed)
                        <x-ui.button type="submit" variant="secondary">
                            <x-slot:icon><x-icon.x /></x-slot:icon>
                            Abbestellen
                        </x-ui.button>
                    @else
                        <x-ui.button type="submit">
                            <x-slot:icon><x-icon.check /></x-slot:icon>
                            Abonnieren
                        </x-ui.button>
                    @endif
                </div>
            </form>
        </x-ui.section>

        <!-- Interests Section -->
        <x-ui.section title="Meine Interessen" class="mt-12 border-t border-line pt-8"
            description="Wählen Sie Kategorien aus, die Sie interessieren. Sie werden über neue Veranstaltungen in diesen Kategorien benachrichtigt.">
            <form action="{{ route('newsletter.interests') }}" method="POST">
                @csrf

                <div class="mb-6 grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-1">
                    @foreach ($categories as $category)
                        <x-ui.checkbox name="category_ids[]" :value="$category->id" :label="$category->name"
                            :hint="$category->description ? Str::limit($category->description, 50) : null"
                            :checked="auth()->user()->isInterestedInCategory($category->id)" />
                    @endforeach
                </div>

                <x-ui.button type="submit">Interessen speichern</x-ui.button>
            </form>
        </x-ui.section>

        <!-- Recommended Events Section -->
        @if ($recommendedEvents->count() > 0)
            <x-ui.section title="Empfohlene Veranstaltungen" class="mt-12 border-t border-line pt-8"
                description="Basierend auf Ihren Interessen empfehlen wir Ihnen diese Veranstaltungen:">
                <x-ui.row-list>
                    @foreach ($recommendedEvents as $event)
                        <x-ui.event-row :event="$event" />
                    @endforeach
                </x-ui.row-list>
            </x-ui.section>
        @endif
    </div>
</x-layouts.app>
