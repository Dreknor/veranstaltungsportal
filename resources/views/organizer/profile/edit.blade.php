<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Organizer-Profil bearbeiten
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('organizer.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Profile Photo Section -->
                        <div class="border-b border-gray-200 pb-6 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profilbild</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Laden Sie ein Profilbild hoch (max. 2MB)</p>

                            <div class="mt-4 flex items-center space-x-6">
                                <div class="flex-shrink-0">
                                    <img class="h-24 w-24 rounded-full object-cover" src="{{ auth()->user()->profilePhotoUrl() }}" alt="Profilbild">
                                </div>

                                <div class="flex-1">
                                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="block w-full text-sm text-gray-900 file:mr-4 file:rounded-md file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100 dark:text-gray-300 dark:file:bg-gray-700 dark:file:text-gray-300">
                                    @error('profile_photo')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror

                                    @if(auth()->user()->profile_photo)
                                        <form method="POST" action="{{ route('organizer.profile.delete-photo') }}" class="mt-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                Profilbild löschen
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Personal Information -->
                        <div class="border-b border-gray-200 pb-6 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Persönliche Informationen</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ihre persönlichen Kontaktdaten</p>

                            <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- First Name -->
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Vorname *</label>
                                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Last Name -->
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nachname *</label>
                                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-Mail *</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telefon</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Bio -->
                            <div class="mt-4">
                                <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Über mich</label>
                                <textarea name="bio" id="bio" rows="3" maxlength="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('bio', $user->bio) }}</textarea>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maximal 1000 Zeichen</p>
                                @error('bio')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Organization Information -->
                        <div class="border-b border-gray-200 pb-6 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Organisationsinformationen</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Informationen über Ihre Organisation oder Institution</p>

                            <div class="mt-4 space-y-4">
                                <!-- Organization Name -->
                                <div>
                                    <label for="organization_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Organisationsname</label>
                                    <input type="text" name="organization_name" id="organization_name" value="{{ old('organization_name', $user->organization_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('organization_name')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Organization Website -->
                                <div>
                                    <label for="organization_website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Website</label>
                                    <input type="url" name="organization_website" id="organization_website" value="{{ old('organization_website', $user->organization_website) }}" placeholder="https://www.beispiel.de" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('organization_website')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Organization Description -->
                                <div>
                                    <label for="organization_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Beschreibung der Organisation</label>
                                    <textarea name="organization_description" id="organization_description" rows="4" maxlength="2000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">{{ old('organization_description', $user->organization_description) }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maximal 2000 Zeichen</p>
                                    @error('organization_description')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Billing Address -->
                        <div class="border-b border-gray-200 pb-6 dark:border-gray-700">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Rechnungsadresse</h3>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ihre Rechnungsadresse für Abrechnungen und Steuerzwecke</p>

                            <div class="mt-4 space-y-4">
                                <!-- Company Name -->
                                <div>
                                    <label for="billing_company" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Firma/Institution</label>
                                    <input type="text" name="billing_company" id="billing_company" value="{{ old('billing_company', $user->billing_company) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('billing_company')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Address -->
                                <div>
                                    <label for="billing_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Straße & Hausnummer</label>
                                    <input type="text" name="billing_address" id="billing_address" value="{{ old('billing_address', $user->billing_address) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('billing_address')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Address Line 2 -->
                                <div>
                                    <label for="billing_address_line2" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresszusatz (optional)</label>
                                    <input type="text" name="billing_address_line2" id="billing_address_line2" value="{{ old('billing_address_line2', $user->billing_address_line2) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('billing_address_line2')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Postal Code -->
                                    <div>
                                        <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postleitzahl</label>
                                        <input type="text" name="billing_postal_code" id="billing_postal_code" value="{{ old('billing_postal_code', $user->billing_postal_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                        @error('billing_postal_code')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- City -->
                                    <div>
                                        <label for="billing_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stadt</label>
                                        <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city', $user->billing_city) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                        @error('billing_city')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- State -->
                                    <div>
                                        <label for="billing_state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bundesland</label>
                                        <input type="text" name="billing_state" id="billing_state" value="{{ old('billing_state', $user->billing_state) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                        @error('billing_state')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Country -->
                                    <div>
                                        <label for="billing_country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Land</label>
                                        <input type="text" name="billing_country" id="billing_country" value="{{ old('billing_country', $user->billing_country ?? 'Deutschland') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                        @error('billing_country')
                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tax ID -->
                                <div>
                                    <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Steuernummer / USt-IdNr.</label>
                                    <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $user->tax_id) }}" placeholder="z.B. DE123456789" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    @error('tax_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-500 dark:hover:bg-blue-600">
                                Profil aktualisieren
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

