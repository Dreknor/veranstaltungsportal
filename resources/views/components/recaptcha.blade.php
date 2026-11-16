@props(['action' => 'submit'])

@if(config('recaptcha.enabled') && (!auth()->check() || !config('recaptcha.skip_for_authenticated')))
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-{{ $action }}">

    {{-- Warnhinweis wenn funktionale Cookies nicht erlaubt sind (app.js blendet ihn über die Klasse "hidden" ein und aus) --}}
    <div id="recaptcha-cookie-warning-{{ $action }}" class="hidden">
        <x-ui.alert tone="warning" title="Sicherheits-Check nicht möglich">
            <p>Für den Login wird reCAPTCHA (Google) als Spam-Schutz benötigt. Bitte erlauben Sie <strong>funktionale Cookies</strong>, damit die Anmeldung funktioniert.</p>
            <x-ui.button variant="secondary" size="sm" class="mt-3"
                onclick="window.showCookiePreferences && window.showCookiePreferences()">
                <x-slot:icon><x-icon.settings /></x-slot:icon>
                Cookie-Einstellungen öffnen
            </x-ui.button>
        </x-ui.alert>
    </div>

    @once
        @push('scripts')
        <script>
            /**
             * reCAPTCHA-Integration: Das Script wird NICHT direkt eingebunden.
             * Es wird erst nach Cookie-Einwilligung durch app.js geladen (DSGVO-konform).
             * Das 'recaptcha:loaded'-Event wird von app.js gefeuert sobald das Script bereit ist.
             * Die Cookie-Warnhinweise werden ebenfalls von app.js gesteuert.
             *
             * WICHTIG: Kein form.cloneNode() – das würde alle Event-Listener anderer
             * Skripte (z.B. Ticket-Buttons) zerstören. Stattdessen wird ein data-Attribut
             * als Flag genutzt um Doppel-Registrierung zu verhindern.
             */
            function initRecaptchaForms() {
                const siteKey = document.querySelector('meta[name="recaptcha-site-key"]')?.content;
                if (!siteKey) return;

                // Warnhinweise ausblenden da reCAPTCHA geladen ist
                document.querySelectorAll('[id^="recaptcha-cookie-warning-"]').forEach(el => el.classList.add('hidden'));

                const forms = document.querySelectorAll('form[data-recaptcha]');
                forms.forEach(form => {
                    // Bereits initialisiert? Überspringen um Doppel-Listener zu vermeiden
                    if (form.dataset.recaptchaInit === '1') return;
                    form.dataset.recaptchaInit = '1';

                    const action = form.getAttribute('data-recaptcha-action') || 'submit';

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const input = form.querySelector('input[name="g-recaptcha-response"]');
                        grecaptcha.ready(function() {
                            grecaptcha.execute(siteKey, { action: action }).then(function(token) {
                                if (input) input.value = token;
                                form.submit();
                            }).catch(function() {
                                form.submit();
                            });
                        });
                    });
                });
            }

            // Falls reCAPTCHA bereits geladen wurde (Consent war schon erteilt)
            if (typeof grecaptcha !== 'undefined') {
                initRecaptchaForms();
            }
            // Sonst auf das Event warten, das app.js nach dem Laden feuert
            document.addEventListener('recaptcha:loaded', initRecaptchaForms);
        </script>
        @endpush
    @endonce

    <x-ui.field-error name="recaptcha" />
@endif

