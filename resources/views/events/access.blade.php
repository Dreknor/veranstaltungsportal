<x-layouts.public title="Private Veranstaltung – Zugangscode erforderlich">
    <div class="mx-auto max-w-page px-page pt-8 pb-16">
        <div class="max-w-form">
            <x-ui.page-header title="Private Veranstaltung" :subtitle="$event->title" :back-href="route('events.index')"
                back-label="Zur Übersicht" />

            <x-ui.box class="flex flex-col gap-6">
                <div class="flex items-start gap-3">
                    <x-icon.lock class="mt-0.5 h-5 w-5 flex-none text-muted" />
                    <p class="m-0 text-base text-ink-soft">
                        Diese Veranstaltung ist privat. Bitte geben Sie den Zugangscode ein, den Sie vom Veranstalter
                        erhalten haben.
                    </p>
                </div>

                <form action="{{ route('events.verify-access', $event->slug) }}" method="POST"
                    class="flex flex-col gap-y-5" data-recaptcha data-recaptcha-action="access_code">
                    @csrf
                    <x-ui.field label="Zugangscode" name="access_code" required autocomplete="off"
                        placeholder="Zugangscode eingeben" />

                    <!-- reCAPTCHA -->
                    <x-recaptcha action="access_code" />
                    <x-ui.field-error name="recaptcha" id="access-recaptcha-error" />

                    <div>
                        <x-ui.button type="submit">Zugang freischalten</x-ui.button>
                    </div>
                </form>
            </x-ui.box>
        </div>
    </div>
</x-layouts.public>
