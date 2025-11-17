<x-layouts.public :title="__('Konto erstellen')">
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="mb-3">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Konto erstellen') }}</h1>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-3" x-data="{ userType: 'participant' }">
                        @csrf
                        <!-- Full Name Input -->
                        <div>
                            <x-forms.input label="Vollständiger Name" name="name" type="text" placeholder="{{ __('Vollständiger Name') }}" autofocus />
                        </div>

                        <!-- Email Input -->
                        <div>
                            <x-forms.input label="E-Mail" name="email" type="email" placeholder="ihre@email.de" />
                        </div>

                        <!-- Account Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Kontotyp') }}
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer transition-all"
                                       :class="userType === 'participant' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                    <input type="radio" name="account_type" value="participant"
                                           x-model="userType"
                                           class="sr-only"
                                           checked>
                                    <div class="flex items-center justify-center mb-2">
                                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 text-center">{{ __('Teilnehmer') }}</span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">{{ __('Ich möchte Events besuchen') }}</span>
                                </label>

                                <label class="relative flex flex-col p-4 border-2 rounded-lg cursor-pointer transition-all"
                                       :class="userType === 'organizer' ? 'border-blue-600 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                    <input type="radio" name="account_type" value="organizer"
                                           x-model="userType"
                                           class="sr-only">
                                    <div class="flex items-center justify-center mb-2">
                                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 text-center">{{ __('Organisator') }}</span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400 text-center mt-1">{{ __('Ich möchte Events erstellen') }}</span>
                                </label>
                            </div>
                            @error('account_type')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Organization Fields (shown only for organizers) -->
                        <div x-show="userType === 'organizer'" x-transition class="space-y-3">
                            <div>
                                <x-forms.input label="Organisation / Firma" name="organization_name" type="text"
                                             placeholder="{{ __('Name Ihrer Organisation') }}" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Beschreibung') }} <span class="text-gray-500">({{ __('optional') }})</span>
                                </label>
                                <textarea name="organization_description" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100"
                                        placeholder="{{ __('Kurze Beschreibung Ihrer Organisation') }}">{{ old('organization_description') }}</textarea>
                                @error('organization_description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <x-forms.input label="Passwort" name="password" type="password" placeholder="••••••••" />
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <x-forms.input label="Passwort bestätigen" name="password_confirmation" type="password"
                                placeholder="••••••••" />
                        </div>
                        <!-- reCAPTCHA -->
                        <x-recaptcha action="register" />

                        <!-- Register Button -->
                        <x-button type="primary" class="w-full">{{ __('Konto erstellen') }}</x-button>
                    </form>

                <!-- Register Button -->
                <x-button type="primary" class="w-full">{{ __('Create Account') }}</x-button>
            </form>

                    <!-- SSO Register Separator -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">{{ __('oder') }}</span>
                        </div>
                    </div>

                    <!-- SSO Registration Buttons -->
                    <div class="space-y-3">
                        <!-- KeyCloak SSO Button -->
                        @if(config('services.keycloak.client_id'))
                        <a href="{{ route('sso.redirect', 'keycloak') }}"
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            {{ __('Mit KeyCloak registrieren') }}
                        </a>
                        @endif

                        <!-- Google SSO Button -->
                        @if(config('services.google.client_id'))
                        <a href="{{ route('sso.redirect', 'google') }}"
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            {{ __('Mit Google registrieren') }}
                        </a>
                        @endif

                        <!-- GitHub SSO Button -->
                        @if(config('services.github.client_id'))
                        <a href="{{ route('sso.redirect', 'github') }}"
                           class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                            {{ __('Mit GitHub registrieren') }}
                        </a>
                        @endif
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Bereits ein Konto?') }}
                            <a href="{{ route('login') }}"
                                class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Jetzt anmelden') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>

