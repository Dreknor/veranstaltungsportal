<x-layouts.public :title="__('Kontaktformular')">
    <div class="mx-auto max-w-page px-page py-[clamp(32px,6vw,64px)]">
        <x-ui.page-header title="Kontakt" subtitle="Haben Sie Fragen oder Anregungen? Kontaktieren Sie uns gerne!" />

        <div class="max-w-form">
            <!-- Kontaktformular (Erfolgsmeldung rendert das Layout über x-ui.flash) -->
            <form action="{{ route('contact.store') }}" method="POST" class="flex flex-col gap-5" data-recaptcha
                data-recaptcha-action="contact">
                @csrf

                <p class="m-0 text-small text-muted">* Pflichtfeld</p>

                <x-ui.field label="Name" name="name" type="text" required autocomplete="name" />

                <x-ui.field label="E-Mail-Adresse" name="email" type="email" required autocomplete="email" />

                <x-ui.field label="Betreff" name="subject" type="text" required />

                <x-ui.textarea label="Nachricht" name="message" rows="6" required
                    hint="Mindestens 10 Zeichen erforderlich" />

                <!-- reCAPTCHA v3 (unsichtbar) -->
                <x-recaptcha action="contact" />

                <x-ui.field-error name="recaptcha" />
                <x-ui.field-error name="g-recaptcha-response" />

                <div>
                    <x-ui.button type="submit">
                        <x-slot:icon><x-icon.mail /></x-slot:icon>
                        Nachricht senden
                    </x-ui.button>
                </div>
            </form>
        </div>

        <!-- Hinweise -->
        <div class="mt-[clamp(48px,6vw,80px)] grid gap-x-8 gap-y-8 md:grid-cols-3">
            <div class="border-t border-line pt-4">
                <x-icon.clock class="h-5 w-5 text-ink" />
                <h2 class="m-0 mt-3 mb-1 text-h3">Schnelle Antwort</h2>
                <p class="m-0 text-base text-muted">
                    Wir antworten in der Regel innerhalb von 24 Stunden auf Ihre Anfrage.
                </p>
            </div>

            <div class="border-t border-line pt-4">
                <x-icon.lock class="h-5 w-5 text-ink" />
                <h2 class="m-0 mt-3 mb-1 text-h3">Sicher &amp; Privat</h2>
                <p class="m-0 text-base text-muted">
                    Ihre Daten sind durch reCAPTCHA geschützt und werden vertraulich behandelt.
                </p>
            </div>

            <div class="border-t border-line pt-4">
                <x-icon.help class="h-5 w-5 text-ink" />
                <h2 class="m-0 mt-3 mb-1 text-h3">Hilfe &amp; Support</h2>
                <p class="m-0 text-base text-muted">
                    Unser Support-Team hilft Ihnen gerne bei Fragen und Problemen.
                </p>
            </div>
        </div>
    </div>
</x-layouts.public>
