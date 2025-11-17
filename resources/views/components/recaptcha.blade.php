@props(['action' => 'submit'])

@if(config('recaptcha.enabled') && (!auth()->check() || !config('recaptcha.skip_for_authenticated')))
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response-{{ $action }}">

    @once
        @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const siteKey = '{{ config('recaptcha.site_key') }}';

                if (!siteKey) {
                    console.error('reCAPTCHA site key not configured');
                    return;
                }

                // Initialize reCAPTCHA for all forms with recaptcha component
                const forms = document.querySelectorAll('form[data-recaptcha]');

                forms.forEach(form => {
                    const action = form.getAttribute('data-recaptcha-action') || 'submit';

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        grecaptcha.ready(function() {
                            grecaptcha.execute(siteKey, { action: action }).then(function(token) {
                                // Find the hidden input in this form
                                const input = form.querySelector('input[name="g-recaptcha-response"]');
                                if (input) {
                                    input.value = token;
                                }
                                // Submit the form
                                form.submit();
                            }).catch(function(error) {
                                console.error('reCAPTCHA error:', error);
                                // Still submit the form, let the server handle the missing token
                                form.submit();
                            });
                        });
                    });
                });
            });
        </script>
        @endpush
    @endonce

    @error('recaptcha')
        <div class="mt-2 text-sm text-red-600 dark:text-red-400">
            {{ $message }}
        </div>
    @enderror
@endif

