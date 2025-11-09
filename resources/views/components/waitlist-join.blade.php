<!-- Waitlist Join Component -->
@props(['event'])

@php
    $isSoldOut = $event->ticketTypes->every(fn($ticket) => $ticket->availableQuantity() === 0);
    $userOnWaitlist = false;

    if (auth()->check()) {
        $userOnWaitlist = \App\Models\EventWaitlist::where('event_id', $event->id)
            ->where('email', auth()->user()->email)
            ->whereIn('status', ['waiting', 'notified'])
            ->exists();
    }
@endphp

@if($isSoldOut)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
        <div class="flex items-start gap-3 mb-4">
            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-2">Event ausverkauft</h3>
                <p class="text-sm text-yellow-800 dark:text-yellow-200 mb-4">
                    Alle Tickets für diese Veranstaltung sind vergriffen. Tragen Sie sich in die Warteliste ein, um benachrichtigt zu werden, wenn Tickets verfügbar werden.
                </p>

                @if($userOnWaitlist)
                    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-4 mb-4">
                        <p class="text-sm text-green-800 dark:text-green-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Sie stehen bereits auf der Warteliste
                        </p>
                    </div>

                    <form method="POST" action="{{ route('waitlist.leave', $event) }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 dark:text-red-400 hover:underline">
                            Von Warteliste entfernen
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('waitlist.join', $event) }}" class="space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="waitlist_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                                <input type="text" name="name" id="waitlist_name" required
                                       value="{{ auth()->check() ? auth()->user()->fullName() : old('name') }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="waitlist_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-Mail *</label>
                                <input type="email" name="email" id="waitlist_email" required
                                       value="{{ auth()->check() ? auth()->user()->email : old('email') }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="waitlist_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                                <input type="tel" name="phone" id="waitlist_phone"
                                       value="{{ auth()->check() ? auth()->user()->phone : old('phone') }}"
                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                            </div>

                            <div>
                                <label for="waitlist_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anzahl Tickets *</label>
                                <select name="quantity" id="waitlist_quantity" required
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        @if($event->ticketTypes->count() > 1)
                            <div>
                                <label for="waitlist_ticket_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ticket-Typ (optional)</label>
                                <select name="ticket_type_id" id="waitlist_ticket_type"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                                    <option value="">Beliebig</option>
                                    @foreach($event->ticketTypes as $ticketType)
                                        <option value="{{ $ticketType->id }}">{{ $ticketType->name }} ({{ number_format($ticketType->price, 2, ',', '.') }} €)</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <button type="submit" class="w-full px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-semibold">
                            Zur Warteliste hinzufügen
                        </button>

                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            Wir benachrichtigen Sie per E-Mail, sobald Tickets verfügbar sind. Sie haben dann 48 Stunden Zeit, um zu buchen.
                        </p>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endif

