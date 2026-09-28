<x-layouts.app title="Meine Buchungen">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-4 sm:py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100">Meine Buchungen</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Übersicht aller Ihrer Fortbildungs-Buchungen</p>
            </div>

            @if($bookings->count() > 0)
                <ul class="space-y-3">
                    @foreach($bookings as $booking)
                        @continue(!$booking->event)
                        @php
                            $event = $booking->event;
                            $isReady = $booking->isReadyForParticipation();
                            $action = match (true) {
                                $booking->status === 'cancelled' => null,
                                $booking->status === 'pending_approval' => ['Wartet auf Freigabe durch den Veranstalter', 'text-amber-700 dark:text-amber-400'],
                                !$booking->isPaymentComplete() && !$booking->isFree() => ['Zahlung offen – Details ansehen', 'text-yellow-700 dark:text-yellow-400'],
                                $booking->canBePersonalized() && !$booking->tickets_personalized => ['Bitte Teilnehmende eintragen', 'text-amber-700 dark:text-amber-400'],
                                default => null,
                            };
                            $badge = match (true) {
                                $booking->status === 'cancelled' => ['Storniert', 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200'],
                                $booking->status === 'pending_approval' => ['Freigabe ausstehend', 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200'],
                                $isReady && $event->end_date?->isPast() => ['Teilgenommen', 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200'],
                                $isReady => ['Bestätigt', 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-200'],
                                default => ['Zahlung offen', 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-200'],
                            };
                        @endphp
                        <li class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                            <a href="{{ route('bookings.show', $booking->booking_number) }}" class="flex gap-4 p-4">
                                @if($event->featured_image)
                                    <img src="{{ Storage::url($event->featured_image) }}" alt=""
                                         class="hidden sm:block w-20 h-20 object-cover rounded-lg shrink-0">
                                @else
                                    <div class="hidden sm:flex w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg items-center justify-center shrink-0">
                                        <x-icon.academic class="w-10 h-10 text-white opacity-60" />
                                    </div>
                                @endif
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <h2 class="font-semibold text-gray-900 dark:text-gray-100">{{ $event->title }}</h2>
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $badge[1] }}">{{ $badge[0] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                                        @if($event->isOnline()) · Online @elseif($event->venue_city) · {{ $event->venue_city }} @endif
                                        · {{ $booking->items->sum('quantity') }} {{ $booking->items->sum('quantity') === 1 ? 'Platz' : 'Plätze' }}
                                        @unless($booking->isFree()) · {{ number_format($booking->total, 2, ',', '.') }} € @endunless
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-mono">{{ $booking->booking_number }}</p>
                                    @if($action)
                                        <p class="text-sm font-medium mt-2 {{ $action[1] }}">→ {{ $action[0] }}</p>
                                    @endif
                                </div>
                            </a>
                            @if($isReady && ($booking->hasTicketDocument() || $booking->canAccessOnlineContent()) && !$event->end_date?->isPast())
                                <div class="flex gap-2 px-4 pb-4 -mt-1">
                                    @if($booking->canAccessOnlineContent() && $event->online_url)
                                        <a href="{{ $event->online_url }}" target="_blank" rel="noopener"
                                           class="flex-1 sm:flex-none text-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Zur Online-Veranstaltung</a>
                                    @endif
                                    @if($booking->hasTicketDocument() && $booking->canSendTickets())
                                        <a href="{{ route('bookings.ticket', $booking->booking_number) }}"
                                           class="flex-1 sm:flex-none text-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm font-medium">Ticket (PDF)</a>
                                    @endif
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 sm:p-12 text-center">
                    <x-icon.ticket class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Noch keine Buchungen</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Entdecken Sie spannende Fortbildungen und sichern Sie sich Ihren Platz.</p>
                    <a href="{{ route('events.index') }}"
                       class="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        Fortbildungen entdecken
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
