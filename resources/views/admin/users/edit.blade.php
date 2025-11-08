<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Benutzer bearbeiten</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $user->name }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-2xl">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-Mail</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Is Organizer -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_organizer" value="1" {{ old('is_organizer', $user->is_organizer) ? 'checked' : '' }}
                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Veranstalter (Legacy)</span>
                        </label>
                    </div>

                    <!-- Roles -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rollen</label>
                        <div class="space-y-2">
                            @foreach($roles as $role)
                                <label class="flex items-center">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           {{ in_array($role->name, $userRoles) ? 'checked' : '' }}
                                           {{ auth()->id() === $user->id && $role->name === 'admin' ? 'disabled' : '' }}
                                           class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ ucfirst($role->name) }}
                                        @if(auth()->id() === $user->id && $role->name === 'admin')
                                            <span class="text-xs text-gray-500">(kann nicht geändert werden)</span>
                                        @endif
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Wählen Sie die Rollen für diesen Benutzer aus.
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-between pt-4">
                        <a href="{{ route('admin.users.index') }}"
                           class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                            Abbrechen
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Speichern
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
