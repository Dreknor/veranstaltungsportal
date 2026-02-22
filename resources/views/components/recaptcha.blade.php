@props(['action' => 'submit'])

@if(config('recaptcha.enabled') && (!auth()->check() || !config('recaptcha.skip_for_authenticated')))
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-{{ $action }}">

    @once
        @push('scripts')
        <script>
            /**
             * reCAPTCHA-Integration: Das Script wird NICHT direkt eingebunden.
             * Es wird erst nach Cookie-Einwilligung durch app.js geladen (DSGVO-konform).
             * Das 'recaptcha:loaded'-Event wird von app.js gefeuert sobald das Script bereit ist.
             */
            function initRecaptchaForms() {
                const siteKey = document.querySelector('meta[name="recaptcha-site-key"]')?.content;
                if (!siteKey) return;

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

