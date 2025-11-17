<x-layouts.public :title="__('Passwort vergessen')">
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Forgot Password Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Passwort vergessen') }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ __('Geben Sie Ihre E-Mail-Adresse ein, um einen Link zum Zurücksetzen des Passworts zu erhalten') }}</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" data-recaptcha data-recaptcha-action="password_reset">
                        @csrf
                        <!-- Email Input -->
                        <div class="mb-4">
                            <x-forms.input name="email" type="email" label="E-Mail" placeholder="ihre@email.de" />
                        </div>

                        <!-- reCAPTCHA -->
                        <x-recaptcha action="password_reset" />

                        <!-- Send Reset Link Button -->
                        <x-button type="primary" buttonType="submit" class="w-full">
                            {{ __('Link zum Zurücksetzen senden') }}
                        </x-button>
                    </form>

                    <!-- Back to Login Link -->
                    <div class="text-center mt-6">
                        <a href="{{ route('login') }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Zurück zur Anmeldung') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
