<x-layouts.public :title="__('Register an account')">
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div class="mb-3">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Register an account') }}</h1>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-3" x-data="{ userType: 'participant' }">
                @csrf
                <!-- Full Name Input -->
                <div>
                    <x-forms.input label="Full Name" name="name" type="text" placeholder="{{ __('Full Name') }}" autofocus />
                </div>

                <!-- Email Input -->
                <div>
                    <x-forms.input label="Email" name="email" type="email" placeholder="your@email.com" />
                </div>

                <!-- Account Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('Account Type') }}
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
                    <x-forms.input label="Password" name="password" type="password" placeholder="••••••••" />
                </div>

                <!-- Confirm Password Input -->
                <div>
                    <x-forms.input label="Confirm Password" name="password_confirmation" type="password"
                        placeholder="••••••••" />
                </div>

                <!-- Register Button -->
                <x-button type="primary" class="w-full">{{ __('Create Account') }}</x-button>
            </form>

            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Already have an account?
                    <a href="{{ route('login') }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ __('Sign in') }}</a>
                </p>
            </div>
        </div>
    </div>
        </div>
    </div>
</x-layouts.public>
