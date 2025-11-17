<x-layouts.public :title="__('Passwort zurücksetzen')">
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Reset Password Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Passwort zurücksetzen') }}</h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Geben Sie Ihre E-Mail-Adresse und Ihr neues Passwort ein.') }}
                        </p>
                    </div>

                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ request()->route('token') }}">

                        <!-- Email Input -->
                        <div class="mb-4">
                            <x-forms.input name="email" type="email" label="E-Mail"
                                value="{{ old('email', request('email')) }}" placeholder="ihre@email.de" />
                        </div>

                        <!-- Password Input -->
                        <div class="mb-4">
                            <x-forms.input name="password" type="password" label="Passwort" placeholder="••••••••" />
                        </div>

                        <!-- Confirm Password Input -->
                        <div class="mb-4">
                            <x-forms.input name="password_confirmation" type="password" label="Passwort bestätigen"
                                placeholder="••••••••" />
                        </div>

                        <!-- Reset Password Button -->
                        <x-button type="primary" buttonType="submit" class="w-full">
                            {{ __('Passwort zurücksetzen') }}
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
