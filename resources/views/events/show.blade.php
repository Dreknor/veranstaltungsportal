<x-layouts.public :title="$event->title">
    <!-- SEO & Social Media Meta Tags -->
    @push('meta')
        <x-meta-tags :event="$event" />
    @endpush

    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section mit Featured Image -->
        <div class="relative bg-gray-900">
            @if($event->featured_image)
                <img src="{{ Storage::url($event->featured_image) }}" alt="{{ $event->title }}" class="w-full h-96 object-cover opacity-60">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 to-transparent"></div>
            @else
                <div class="h-96 bg-gradient-to-br from-blue-600 to-purple-700"></div>
            @endif

            <div class="absolute bottom-0 left-0 right-0 p-8">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                              style="background-color: {{ $event->category->color }}; color: white;">
                            {{ $event->category->name }}
                        </span>
                        @if($event->is_cancelled)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-500 text-white">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Abgesagt
                            </span>
                        @elseif($event->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500 text-white">
                                Featured
                            </span>
                        @endif
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">{{ $event->title }}</h1>
                    <div class="flex items-center text-white text-lg">
                        <x-icon.user class="w-5 h-5 mr-2" />
                        Veranstalter: {{ $event->getOrganizerName() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Hauptinhalt -->
                <div class="lg:col-span-2 space-y-6">
                    @if($event->is_cancelled)
                        <!-- Absage-Hinweis -->
                        <div class="bg-red-50 border-2 border-red-500 rounded-lg p-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-xl font-bold text-red-900 mb-2">Diese Veranstaltung wurde abgesagt</h3>
                                    <p class="text-red-800">
                                        Diese Veranstaltung findet nicht wie geplant statt.
                                        @if($event->cancellation_reason)
                                            <br><strong>Grund:</strong> {{ $event->cancellation_reason }}
                                        @endif
                                    </p>
                                    @if($event->cancelled_at)
                                        <p class="text-sm text-red-700 mt-2">
                                            Abgesagt am: {{ $event->cancelled_at->format('d.m.Y H:i') }} Uhr
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Event Details -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Über die Veranstaltung</h2>
                        <div class="prose max-w-none">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    <!-- Video -->
                    @if($event->video_url)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Video</h2>
                            <div class="aspect-w-16 aspect-h-9">
                                <iframe src="{{ $event->video_url }}" frameborder="0" allowfullscreen class="w-full h-64 rounded-lg"></iframe>
                            </div>
                        </div>
                    @endif

                    <!-- Livestream -->
                    @if($event->livestream_url)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">
                                <x-icon.video class="w-6 h-6 inline-block mr-2 text-red-600" />
                                Live Stream
                            </h2>
                            <a href="{{ $event->livestream_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                {{ $event->livestream_url }}
                            </a>
                        </div>
                    @endif

                    <!-- Bewertungen -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Bewertungen</h2>

                        @if($event->reviews->where('is_approved', true)->count() > 0)
                            <div class="mb-6 flex items-center gap-4">
                                <div class="text-4xl font-bold text-gray-900">
                                    {{ number_format($event->averageRating(), 1) }}
                                </div>
                                <div>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $event->averageRating())
                                                <x-icon.star-filled class="w-5 h-5" />
                                            @else
                                                <x-icon.star class="w-5 h-5" />
                                            @endif
                                        @endfor
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $event->reviews->where('is_approved', true)->count() }} Bewertungen</p>
                                </div>
                            </div>

                            <div class="space-y-4 mb-6">
                                @foreach($event->reviews->where('is_approved', true)->take(5) as $review)
                                    <div class="border-b pb-4 last:border-b-0">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="font-medium">{{ $review->user->name }}</span>
                                            <div class="flex text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <x-icon.star-filled class="w-4 h-4" />
                                                    @else
                                                        <x-icon.star class="w-4 h-4" />
                                                    @endif
                                                @endfor
                                            </div>
                                            <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-gray-700">{{ $review->comment }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 mb-6">Noch keine Bewertungen vorhanden.</p>
                        @endif

                        <!-- Review Form for Authenticated Users -->
                        @auth
                            @php
                                $userReview = $event->reviews()->where('user_id', auth()->id())->first();
                                $hasAttended = \App\Models\Booking::where('user_id', auth()->id())
                                    ->where('event_id', $event->id)
                                    ->where('status', 'confirmed')
                                    ->exists();
                            @endphp

                            @if($hasAttended && !$userReview)
                                <div class="mt-6 pt-6 border-t">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Deine Bewertung</h3>
                                    <form action="{{ route('events.reviews.store', $event) }}" method="POST" class="space-y-4">
                                        @csrf

                                        <!-- Star Rating -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Bewertung *</label>
                                            <div class="flex gap-2" x-data="{ rating: 0, hoverRating: 0 }">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button"
                                                            @click="rating = {{ $i }}"
                                                            @mouseenter="hoverRating = {{ $i }}"
                                                            @mouseleave="hoverRating = 0"
                                                            class="text-3xl transition-colors"
                                                            :class="(hoverRating >= {{ $i }} || (rating >= {{ $i }} && hoverRating === 0)) ? 'text-yellow-400' : 'text-gray-300'">
                                                        ★
                                                    </button>
                                                @endfor
                                                <input type="hidden" name="rating" x-model="rating" required>
                                            </div>
                                            @error('rating')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Comment -->
                                        <div>
                                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                                Kommentar (optional)
                                            </label>
                                            <textarea name="comment" id="comment" rows="4"
                                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                      placeholder="Teile deine Erfahrungen mit anderen Teilnehmern...">{{ old('comment') }}</textarea>
                                            @error('comment')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <button type="submit"
                                                class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                                            Bewertung abgeben
                                        </button>
                                    </form>
                                </div>
                            @elseif($userReview)
                                <div class="mt-6 pt-6 border-t">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium text-blue-900">Deine Bewertung wurde eingereicht</p>
                                                <p class="text-sm text-blue-700 mt-1">
                                                    @if($userReview->is_approved)
                                                        Deine Bewertung ist veröffentlicht.
                                                    @else
                                                        Deine Bewertung wird gerade überprüft und in Kürze veröffentlicht.
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif(!$hasAttended && $event->start_date->isPast())
                                <div class="mt-6 pt-6 border-t">
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                        <p class="text-sm text-gray-600">
                                            Nur Teilnehmer können eine Bewertung abgeben.
                                        </p>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="mt-6 pt-6 border-t">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                    <p class="text-sm text-gray-600 mb-3">
                                        Melde dich an, um eine Bewertung abzugeben.
                                    </p>
                                    <a href="{{ route('login') }}"
                                       class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                                        Jetzt anmelden
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>

                    <!-- Ähnliche Events -->
                    @if($relatedEvents->count() > 0)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">Ähnliche Veranstaltungen</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @foreach($relatedEvents as $relatedEvent)
                                    <a href="{{ route('events.show', $relatedEvent->slug) }}" class="block group">
                                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 rounded-lg overflow-hidden mb-2">
                                            @if($relatedEvent->featured_image)
                                                <img src="{{ Storage::url($relatedEvent->featured_image) }}" alt="{{ $relatedEvent->title }}" class="w-full h-32 object-cover group-hover:scale-105 transition">
                                            @else
                                                <div class="w-full h-32 bg-gradient-to-br from-blue-400 to-purple-500"></div>
                                            @endif
                                        </div>
                                        <h3 class="font-medium text-gray-900 group-hover:text-blue-600 line-clamp-2">{{ $relatedEvent->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $relatedEvent->start_date->format('d.m.Y') }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Buchungs-Box -->
                    <div class="bg-white rounded-lg shadow-lg p-6 border-2 border-blue-500 sticky top-4">
                        @if($event->is_cancelled)
                            <!-- Abgesagt-Meldung -->
                            <div class="text-center p-6 bg-red-50 border border-red-200 rounded-lg">
                                <svg class="w-16 h-16 mx-auto text-red-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <p class="font-bold text-red-900 text-lg mb-2">Event wurde abgesagt</p>
                                <p class="text-sm text-red-700">Diese Veranstaltung findet nicht statt.</p>
                                @if($event->cancellation_reason)
                                    <div class="mt-3 pt-3 border-t border-red-200">
                                        <p class="text-sm text-red-700 font-medium mb-1">Grund:</p>
                                        <p class="text-sm text-red-600">{{ $event->cancellation_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center mb-4">
                                @php
                                    $minPrice = $event->getMinimumPrice();
                                @endphp
                                @if($minPrice && $minPrice > 0)
                                    <div class="text-sm text-gray-600">Ab</div>
                                    <div class="text-4xl font-bold text-blue-600">{{ number_format($minPrice, 2, ',', '.') }} €</div>
                                    <div class="text-sm text-gray-600">pro Person</div>
                                @else
                                    <div class="text-3xl font-bold text-green-600">Kostenlos</div>
                                @endif
                            </div>

                            @if($event->hasAvailableTickets())
                                <a href="{{ route('bookings.create', $event) }}"
                                   class="block w-full text-center px-6 py-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-bold text-lg shadow-md hover:shadow-lg">
                                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    Jetzt buchen
                                </a>

                                @if($event->ticketTypes->count() > 0)
                                    <div class="mt-4 pt-4 border-t">
                                        <div class="text-sm font-medium text-gray-700 mb-2">Verfügbare Tickets:</div>
                                        <div class="space-y-2">
                                            @foreach($event->ticketTypes->where('is_available', true) as $ticket)
                                                <div class="flex justify-between text-sm">
                                                    <span class="text-gray-600">{{ $ticket->name }}</span>
                                                    <span class="font-medium text-gray-900">{{ number_format($ticket->price, 2, ',', '.') }} €</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($event->max_attendees)
                                    <div class="mt-4 text-center">
                                        <div class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                            </svg>
                                            Noch {{ $event->availableTickets() }} Plätze frei
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="text-center p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <svg class="w-12 h-12 mx-auto text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="font-medium text-red-900">Ausgebucht</p>
                                    <p class="text-sm text-red-700 mt-1">Keine Tickets mehr verfügbar</p>
                                </div>
                            @endif
                        @endif

                        @auth
                            <button onclick="toggleFavorite({{ $event->id }})"
                                    id="favorite-btn-sidebar-{{ $event->id }}"
                                    class="block w-full text-center px-4 py-2 border-2 rounded-lg transition font-medium mt-3
                                           {{ auth()->user()->hasFavorited($event) ? 'border-red-500 text-red-600 bg-red-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                <svg class="w-5 h-5 inline-block mr-2" fill="{{ auth()->user()->hasFavorited($event) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span id="favorite-text-sidebar-{{ $event->id }}">
                                    {{ auth()->user()->hasFavorited($event) ? 'Favorit' : 'Zu Favoriten' }}
                                </span>
                            </button>
                        @endauth
                    </div>

                    <!-- Datum & Zeit -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Datum & Zeit</h3>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <x-icon.calendar class="w-5 h-5 text-gray-400 mr-3 mt-0.5" />
                                <div>
                                    <div class="font-medium text-gray-900">{{ $event->start_date->format('d. F Y') }}</div>
                                    <div class="text-sm text-gray-600">{{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }} Uhr</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Veranstaltungsort -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="font-bold text-gray-900 mb-4">Veranstaltungsort</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="font-medium text-gray-900">{{ $event->venue_name }}</div>
                                <div class="text-sm text-gray-600">{{ $event->venue_address }}</div>
                                <div class="text-sm text-gray-600">{{ $event->venue_postal_code }} {{ $event->venue_city }}</div>
                                <div class="text-sm text-gray-600">{{ $event->venue_country }}</div>
                            </div>

                            @if($event->directions)
                                <div class="pt-3 border-t">
                                    <p class="text-sm text-gray-700">{{ $event->directions }}</p>
                                </div>
                            @endif

                            @if($event->venue_latitude && $event->venue_longitude)
                                <a href="https://www.google.com/maps?q={{ $event->venue_latitude }},{{ $event->venue_longitude }}"
                                   target="_blank"
                                   class="block w-full text-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <x-icon.location class="w-4 h-4 inline-block mr-2" />
                                    Auf Karte anzeigen
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Tickets -->
                    @if($event->ticketTypes->count() > 0)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="font-bold text-gray-900 mb-4">Tickets</h3>
                            <div class="space-y-3">
                                @foreach($event->ticketTypes as $ticket)
                                    <div class="border-b pb-3 last:border-b-0">
                                        <div class="flex justify-between items-start mb-1">
                                            <span class="font-medium text-gray-900">{{ $ticket->name }}</span>
                                            <span class="font-bold text-blue-600">{{ number_format($ticket->price, 2, ',', '.') }} €</span>
                                        </div>
                                        @if($ticket->description)
                                            <p class="text-sm text-gray-600">{{ $ticket->description }}</p>
                                        @endif
                                        @if($ticket->quantity)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Noch {{ $ticket->availableQuantity() }} verfügbar
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <!-- Waitlist if sold out -->
                            <x-waitlist-join :event="$event" />

                            @php
                                $hasAvailableTickets = $event->ticketTypes->some(fn($ticket) => $ticket->availableQuantity() > 0);
                            @endphp

                            @if($hasAvailableTickets)
                                <a href="{{ route('bookings.create', $event) }}"
                                   class="block w-full text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold mt-4">
                                    Jetzt Tickets buchen
                                </a>
                            @endif

                            @auth
                                <button onclick="toggleFavorite({{ $event->id }})"
                                        id="favorite-btn-{{ $event->id }}"
                                        class="block w-full text-center px-6 py-3 border-2 rounded-lg transition font-semibold mt-2
                                               {{ auth()->user()->hasFavorited($event) ? 'border-red-500 text-red-600 bg-red-50' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                    <x-icon.heart class="w-5 h-5 inline-block mr-2"
                                                  fill="{{ auth()->user()->hasFavorited($event) ? 'currentColor' : 'none' }}" />
                                    <span id="favorite-text-{{ $event->id }}">
                                        {{ auth()->user()->hasFavorited($event) ? 'Aus Favoriten entfernen' : 'Zu Favoriten hinzufügen' }}
                                    </span>
                                </button>
                            @endauth
                        </div>
                    @endif

                    <!-- Veranstalter Info -->
                    @if($event->organizer_info || $event->organizer_email || $event->organizer_phone || $event->organizer_website)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="font-bold text-gray-900 mb-4">Veranstalter</h3>
                            <div class="space-y-2">
                                @if($event->organizer_info)
                                    <p class="text-sm text-gray-700">{{ $event->organizer_info }}</p>
                                @endif
                                @if($event->organizer_email)
                                    <a href="mailto:{{ $event->organizer_email }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                        <x-icon.mail class="w-4 h-4 mr-2" />
                                        {{ $event->organizer_email }}
                                    </a>
                                @endif
                                @if($event->organizer_phone)
                                    <a href="tel:{{ $event->organizer_phone }}" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                        <x-icon.phone class="w-4 h-4 mr-2" />
                                        {{ $event->organizer_phone }}
                                    </a>
                                @endif
                                @if($event->organizer_website)
                                    <a href="{{ $event->organizer_website }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 flex items-center">
                                        <x-icon.globe class="w-4 h-4 mr-2" />
                                        Website besuchen
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Social Share -->
                    <x-social-share :event="$event" :shareUrls="$shareUrls" :shareableLink="$shareableLink" />

                    <!-- Calendar Export -->
                    <x-calendar-export :event="$event" />
                </div>
            </div>
        </div>
    </div>

    @auth
    @push('scripts')
    <script>
        function toggleFavorite(eventId) {
            const btn = document.getElementById(`favorite-btn-${eventId}`);
            const text = document.getElementById(`favorite-text-${eventId}`);
            const icon = btn.querySelector('svg');

            fetch(`/events/${eventId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.favorited) {
                    btn.className = 'block w-full text-center px-6 py-3 border-2 rounded-lg transition font-semibold mt-2 border-red-500 text-red-600 bg-red-50';
                    text.textContent = 'Aus Favoriten entfernen';
                    icon.setAttribute('fill', 'currentColor');
                } else {
                    btn.className = 'block w-full text-center px-6 py-3 border-2 rounded-lg transition font-semibold mt-2 border-gray-300 text-gray-700 hover:bg-gray-50';
                    text.textContent = 'Zu Favoriten hinzufügen';
                    icon.setAttribute('fill', 'none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
            });
        }
    </script>
    @endpush
    @endauth
</x-layouts.public>

