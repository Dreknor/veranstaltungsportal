<x-layouts.app>
    <!-- Breadcrumbs -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">{{ __('Dashboard') }}</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">{{ __('Interessen & Newsletter') }}</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Interessen & Newsletter') }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Verwalten Sie Ihre Interessen und Newsletter-Einstellungen') }}</p>
    </div>

    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Navigation -->
            @include('settings.partials.navigation')

            <!-- Content -->
            <div class="flex-1">
                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Es gab Fehler:</span>
                        </div>
                        <ul class="list-disc list-inside ml-8">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Newsletter Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Newsletter-Abonnement</h2>

                        <form action="{{ route('newsletter.subscribe') }}" method="POST">
                            @csrf

                            <div class="flex items-center justify-between py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100">Newsletter erhalten</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        Erhalten Sie regelmäßig Updates über neue Veranstaltungen
                                    </p>
                                    @if(auth()->user()->newsletter_subscribed && auth()->user()->newsletter_subscribed_at)
                                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                                            ✓ Abonniert seit: {{ auth()->user()->newsletter_subscribed_at->format('d.m.Y') }}
                                        </p>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg font-medium transition-colors
                                        {{ auth()->user()->newsletter_subscribed
                                            ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800'
                                            : 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600' }}">
                                        @if(auth()->user()->newsletter_subscribed)
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Abbestellen
                                        @else
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Abonnieren
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Interests Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Meine Interessen</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            Wählen Sie Kategorien aus, die Sie interessieren. Sie werden über neue Veranstaltungen in diesen Kategorien benachrichtigt.
                        </p>

                        <form action="{{ route('newsletter.interests') }}" method="POST">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                                @foreach($categories as $category)
                                    <label class="flex items-center p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-blue-500 dark:hover:border-blue-400 transition-colors {{ auth()->user()->isInterestedInCategory($category->id) ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                        <input type="checkbox"
                                               name="category_ids[]"
                                               value="{{ $category->id }}"
                                               {{ auth()->user()->isInterestedInCategory($category->id) ? 'checked' : '' }}
                                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center">
                                                @if($category->icon)
                                                    <i class="{{ $category->icon }} mr-2" style="color: {{ $category->color }}"></i>
                                                @endif
                                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $category->name }}</span>
                                            </div>
                                            @if($category->description)
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($category->description, 50) }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-end gap-4">
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Interessen speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recommended Events Section -->
                @if($recommendedEvents->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Empfohlene Veranstaltungen</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Basierend auf Ihren Interessen empfehlen wir Ihnen diese Veranstaltungen:
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($recommendedEvents as $event)
                                    <a href="{{ route('events.show', $event) }}" class="group">
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                                            @if($event->featured_image)
                                                <img src="{{ asset('storage/' . $event->featured_image) }}"
                                                     alt="{{ $event->title }}"
                                                     class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-40 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                                    <i class="fas fa-calendar-alt text-white text-4xl"></i>
                                                </div>
                                            @endif
                                            <div class="p-4">
                                                <div class="flex items-center mb-2">
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                          style="background-color: {{ $event->category->color }}20; color: {{ $event->category->color }}">
                                                        {{ $event->category->name }}
                                                    </span>
                                                </div>
                                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400">
                                                    {{ $event->title }}
                                                </h3>
                                                <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                                    <i class="far fa-calendar mr-2"></i>
                                                    {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>

