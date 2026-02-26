@props(['action' => 'submit'])

@if(config('recaptcha.enabled') && (!auth()->check() || !config('recaptcha.skip_for_authenticated')))
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-{{ $action }}">

    {{-- Warnhinweis wenn funktionale Cookies nicht erlaubt sind --}}
    <div id="recaptcha-cookie-warning-{{ $action }}"
         class="hidden rounded-md border border-amber-300 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-600 p-3 text-sm text-amber-800 dark:text-amber-300">
        <div class="flex items-start gap-2">
            <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
            </svg>
            <div>
                <p class="font-medium">Sicherheits-Check nicht möglich</p>
                <p class="mt-1">Für den Login wird reCAPTCHA (Google) als Spam-Schutz benötigt. Bitte erlauben Sie <strong>funktionale Cookies</strong>, damit die Anmeldung funktioniert.</p>
                <button type="button"
                        onclick="window.showCookiePreferences && window.showCookiePreferences()"
                        class="mt-2 inline-flex items-center gap-1 rounded bg-amber-600 hover:bg-amber-700 px-3 py-1.5 text-xs font-medium text-white transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Cookie-Einstellungen öffnen
                </button>
            </div>
        </div>
    </div>

    @once
        @push('scripts')
        <script>
            /**
             * reCAPTCHA-Integration: Das Script wird NICHT direkt eingebunden.
             * Es wird erst nach Cookie-Einwilligung durch app.js geladen (DSGVO-konform).
             * Das 'recaptcha:loaded'-Event wird von app.js gefeuert sobald das Script bereit ist.
             * Die Cookie-Warnhinweise werden ebenfalls von app.js gesteuert.
             */
            function initRecaptchaForms() {
                const siteKey = document.querySelector('meta[name="recaptcha-site-key"]')?.content;
                if (!siteKey) return;

                // Warnhinweise ausblenden da reCAPTCHA geladen ist
                document.querySelectorAll('[id^="recaptcha-cookie-warning-"]').forEach(el => el.classList.add('hidden'));

                const forms = document.querySelectorAll('form[data-recaptcha]');
                forms.forEach(form => {
                    const action = form.getAttribute('data-recaptcha-action') || 'submit';
                    // Vorherigen Listener entfernen (verhindert Doppel-Submission)
                    const newForm = form.cloneNode(true);
                    form.parentNode.replaceChild(newForm, form);

                    newForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        grecaptcha.ready(function() {
                            grecaptcha.execute(siteKey, { action: action }).then(function(token) {
                                const input = newForm.querySelector('input[name="g-recaptcha-response"]');
                                if (input) input.value = token;
                                newForm.submit();
                            }).catch(function() {
                                newForm.submit();
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

    @error('recaptcha')
        <div class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ $message }}
        </div>
    @enderror
@endif

