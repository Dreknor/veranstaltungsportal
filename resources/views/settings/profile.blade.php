<x-layouts.app>
    <!-- Breadcrumbs -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('settings.profile.edit') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Profile') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Profile') }}</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Profile') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Update your name and email address') }}</p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Navigation -->
            @include('settings.partials.navigation')

            <!-- Profile Content -->
            <div class="flex-1">
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                    <div class="p-6">
                        <!-- Profile Form -->
                        <form class="max-w-2xl mb-10" action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <!-- Profile Photo Section -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                    {{ __('Profile Photo') }}
                                </label>
                                <div class="flex items-center space-x-6">
                                    <div class="shrink-0">
                                        <img class="h-24 w-24 object-cover rounded-full ring-4 ring-gray-200 dark:ring-gray-700"
                                             src="{{ $user->profilePhotoUrl() }}"
                                             alt="{{ $user->fullName() }}">
                                    </div>
                                    <div class="flex-1">
                                        <input type="file"
                                               name="profile_photo"
                                               id="profile_photo"
                                               accept="image/*"
                                               class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                      file:mr-4 file:py-2 file:px-4
                                                      file:rounded-md file:border-0
                                                      file:text-sm file:font-semibold
                                                      file:bg-blue-50 file:text-blue-700
                                                      hover:file:bg-blue-100
                                                      dark:file:bg-blue-900 dark:file:text-blue-300
                                                      dark:hover:file:bg-blue-800">
                                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            {{ __('JPG, PNG or GIF (MAX. 2MB)') }}
                                        </p>
                                        @if($user->profile_photo)
                                            <button type="button"
                                                    onclick="if(confirm('{{ __('Are you sure you want to delete your profile photo?') }}')) { document.getElementById('delete-photo-form').submit(); }"
                                                    class="mt-2 text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                                {{ __('Remove photo') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                @error('profile_photo')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-forms.input label="First Name" name="first_name" type="text"
                                        value="{{ old('first_name', $user->first_name) }}" />
                                </div>
                                <div>
                                    <x-forms.input label="Last Name" name="last_name" type="text"
                                        value="{{ old('last_name', $user->last_name) }}" />
                                </div>
                            </div>

                            <div class="mb-4">
                                <x-forms.input label="Display Name (Username)" name="name" type="text"
                                    value="{{ old('name', $user->name) }}" />
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('This is how other users will see you.') }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <x-forms.input label="Email" name="email" type="email"
                                    value="{{ old('email', $user->email) }}" />
                            </div>

                            <!-- Account Type Display (read-only) -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Account Type') }}
                                </label>
                                <div class="flex items-center space-x-2 px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md">
                                    @if($user->isOrganizer())
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ __('Organisator') }}</span>
                                    @else
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ __('Teilnehmer') }}</span>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Contact support to change your account type') }}
                                </p>
                            </div>


                            <div class="mb-4">
                                <x-forms.input label="Phone" name="phone" type="tel"
                                    value="{{ old('phone', $user->phone) }}" />
                            </div>

                            <div class="mb-6">
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('Bio') }}
                                </label>
                                <textarea name="bio"
                                          id="bio"
                                          rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm
                                                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                 dark:bg-gray-700 dark:text-gray-100"
                                          maxlength="1000">{{ old('bio', $user->bio) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ __('Brief description for your profile. Maximum 1000 characters.') }}
                                </p>
                                @error('bio')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-button type="primary">{{ __('Save') }}</x-button>
                            </div>
                        </form>

                        <!-- Hidden form for deleting photo -->
                        @if($user->profile_photo)
                            <form id="delete-photo-form" action="{{ route('settings.profile.photo.delete') }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endif

                        <!-- Delete Account Section -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                            <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-1">
                                {{ __('Delete account') }}
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ __('Delete your account and all of its resources') }}
                            </p>
                            <form action="{{ route('settings.profile.destroy') }}" method="POST"
                                onsubmit="return confirm('{{ __('Are you sure you want to delete your account?') }}')">
                                @csrf
                                @method('DELETE')
                                <x-button type="danger">{{ __('Delete account') }}</x-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
