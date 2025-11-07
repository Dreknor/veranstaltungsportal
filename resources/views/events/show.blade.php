<x-layouts.public :title="$event->title">
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
                        @if($event->is_featured)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-500 text-white">
                                Featured
                            </span>
                        @endif
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-white mb-2">{{ $event->title }}</h1>
                    <div class="flex items-center text-white text-lg">
                        <x-icon.user class="w-5 h-5 mr-2" />
                        Veranstalter: {{ $event->user->name }}
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Hauptinhalt -->
                <div class="lg:col-span-2 space-y-6">
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

                            <a href="{{ route('bookings.create', $event) }}"
                               class="block w-full text-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold mt-4">
                                Jetzt Tickets buchen
                            </a>
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
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>

