<x-layouts.public :title="__('Kontaktformular')">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="mb-12 text-center">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">Kontakt</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">
                Haben Sie Fragen oder Anregungen? Kontaktieren Sie uns gerne!
            </p>
        </div>

        <!-- Contact Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-8 sm:p-12">
                <!-- Success Message -->
                @if (session('success'))
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="mb-8 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="ml-auto text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div x-data="{ show: true }"
                         x-show="show"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="mb-8 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-2">Es gab ein Problem mit Ihrer Anfrage:</h3>
                                <ul class="list-disc list-inside space-y-1 text-sm text-red-700 dark:text-red-300">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button @click="show = false" class="ml-auto text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 flex-shrink-0">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Contact Form -->
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6" data-recaptcha data-recaptcha-action="contact">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 @error('name') border-red-500 @enderror"
                               placeholder="Ihr Name"
                               required>
                        @error('name')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            E-Mail-Adresse <span class="text-red-600">*</span>
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 @error('email') border-red-500 @enderror"
                               placeholder="ihre@email.de"
                               required>
                        @error('email')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="subject" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Betreff <span class="text-red-600">*</span>
                        </label>
                        <input type="text"
                               id="subject"
                               name="subject"
                               value="{{ old('subject') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 @error('subject') border-red-500 @enderror"
                               placeholder="Worum geht es?"
                               required>
                        @error('subject')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
                            Nachricht <span class="text-red-600">*</span>
                        </label>
                        <textarea id="message"
                                  name="message"
                                  rows="6"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 @error('message') border-red-500 @enderror resize-none"
                                  placeholder="Ihre Nachricht..."
                                  required>{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Mindestens 10 Zeichen erforderlich</p>
                    </div>

                    <!-- reCAPTCHA v3 (invisible) -->
                    <x-recaptcha action="contact" />

                    @error('recaptcha')
                        <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                    @enderror
                    @error('g-recaptcha-response')
                        <p class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</p>
                    @enderror

                    <!-- Submit Button -->
                    <div>
                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Nachricht senden
                        </button>
                    </div>

                    <!-- Info Text -->
                    <p class="text-center text-sm text-gray-600 dark:text-gray-400 mt-6">
                        <span class="text-red-600">*</span> = Erforderliche Felder
                    </p>
                </form>
            </div>
        </div>

        <!-- Info Section -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Response Time -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900 mb-4">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Schnelle Antwort</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Wir antworten in der Regel innerhalb von 24 Stunden auf Ihre Anfrage.
                </p>
            </div>

            <!-- Security -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Sicher & Privat</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Ihre Daten sind durch reCAPTCHA geschützt und werden vertraulich behandelt.
                </p>
            </div>

            <!-- Support -->
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900 mb-4">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5-4a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Hilfe & Support</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Unser Support-Team hilft Ihnen gerne bei Fragen und Problemen.
                </p>
            </div>
        </div>
    </div>
</x-layouts.public>

