<x-layouts.public :title="__('E-Mail bestätigen')">
    <div class="min-h-[calc(100vh-12rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Verify Email Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('E-Mail-Adresse bestätigen') }}
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ __('Bitte überprüfen Sie Ihre E-Mails auf einen Bestätigungslink.') }}<br>
                            {{ __('Falls Sie keine E-Mail erhalten haben, können Sie unten eine neue anfordern.') }}
                        </p>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('Ein neuer Bestätigungslink wurde an Ihre E-Mail-Adresse gesendet.') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.store') }}">
                        @csrf
                        <x-button type="primary" buttonType="submit" class="w-full">
                            {{ __('Bestätigungs-E-Mail erneut senden') }}
                        </x-button>
                    </form>

                    <div class="text-center mt-6">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                {{ __('Abmelden') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
