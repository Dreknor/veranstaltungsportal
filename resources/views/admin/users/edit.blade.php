<x-layouts.app>
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}"
               class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                ← Zurück zur Liste
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Benutzer bearbeiten</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $user->name }} &middot; {{ $user->email }}</p>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-200 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-200 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif
        @if (session('info'))
            <div class="mb-6 bg-blue-100 dark:bg-blue-900 border border-blue-400 dark:border-blue-700 text-blue-700 dark:text-blue-200 px-4 py-3 rounded relative">
                {{ session('info') }}
            </div>
        @endif

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- ===== Stammdaten ===== --}}
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Stammdaten</h2>
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            {{-- Anzeigename --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Anzeigename <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Vor- und Nachname --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vorname</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('first_name') border-red-500 @enderror">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nachname</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('last_name') border-red-500 @enderror">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- E-Mail --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    E-Mail-Adresse <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                @if($user->hasVerifiedEmail())
                                    <p class="mt-1 text-xs text-green-600 dark:text-green-400">
                                        ✓ Verifiziert am {{ $user->email_verified_at->format('d.m.Y H:i') }} Uhr
                                    </p>
                                @else
                                    <p class="mt-1 text-xs text-amber-600 dark:text-amber-400">
                                        ⚠ Noch nicht verifiziert
                                    </p>
                                @endif
                            </div>

                            {{-- Telefon --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Rollen --}}
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
                                        @if(auth()->id() === $user->id && $role->name === 'admin' && in_array($role->name, $userRoles))
                                            <input type="hidden" name="roles[]" value="admin">
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex justify-between pt-2">
                                <div class="flex gap-3">
                                    <a href="{{ route('admin.users.index') }}"
                                       class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500">
                                        Abbrechen
                                    </a>
                                    @if($user->hasRole('organizer'))
                                        <a href="{{ route('admin.organizer-fees.edit', $user) }}"
                                           class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                                            💰 Individuelle Gebühren
                                        </a>
                                    @endif
                                </div>
                                <button type="submit"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    Speichern
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== Sidebar: Aktionen ===== --}}
            <div class="space-y-6">

                {{-- E-Mail-Verifizierung --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">E-Mail-Verifizierung</h2>

                    @if($user->hasVerifiedEmail())
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Die E-Mail ist
                            <span class="font-medium text-green-600 dark:text-green-400">verifiziert</span>
                            ({{ $user->email_verified_at->format('d.m.Y') }}).
                        </p>
                        <form action="{{ route('admin.users.unverify-email', $user) }}" method="POST"
                              onsubmit="return confirm('Verifizierung wirklich aufheben?');">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-amber-500 text-white rounded-md hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 text-sm">
                                ✗ Verifizierung aufheben
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Die E-Mail ist
                            <span class="font-medium text-amber-600 dark:text-amber-400">noch nicht verifiziert</span>.
                        </p>
                        <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
                                ✓ E-Mail als verifiziert markieren
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Passwort zurücksetzen --}}
                @if(!$user->isSsoUser())
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Passwort zurücksetzen</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Setzen Sie direkt ein neues Passwort für diesen Benutzer. Der Benutzer wird ausgeloggt.
                        </p>

                        <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                              x-data="{ show: false }">
                            @csrf
                            <div class="space-y-3">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Neues Passwort <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input :type="show ? 'text' : 'password'"
                                               name="password" id="password"
                                               required minlength="8"
                                               autocomplete="new-password"
                                               class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 pr-10 @error('password') border-red-500 @enderror">
                                        <button type="button" @click="show = !show"
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Passwort bestätigen <span class="text-red-500">*</span>
                                    </label>
                                    <input :type="show ? 'text' : 'password'"
                                           name="password_confirmation" id="password_confirmation"
                                           required minlength="8"
                                           autocomplete="new-password"
                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <button type="submit"
                                        onclick="return confirm('Passwort wirklich zurücksetzen? Der Benutzer wird ausgeloggt.')"
                                        class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 text-sm mt-1">
                                    🔑 Passwort zurücksetzen
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">Passwort zurücksetzen</h2>
                        <p class="text-sm text-amber-600 dark:text-amber-400">
                            ℹ Dieser Benutzer verwendet SSO ({{ $user->ssoProviderName() }}) und hat kein lokales Passwort.
                        </p>
                    </div>
                @endif

                {{-- Benutzer-Infos --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Benutzerinfo</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">ID</dt>
                            <dd class="text-gray-900 dark:text-gray-100 font-mono">{{ $user->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Registriert</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500 dark:text-gray-400">Zuletzt aktiv</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d.m.Y H:i') }}</dd>
                        </div>
                        @if($user->isSsoUser())
                            <div class="flex justify-between">
                                <dt class="text-gray-500 dark:text-gray-400">SSO</dt>
                                <dd class="text-purple-600 dark:text-purple-400">{{ $user->ssoProviderName() }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
