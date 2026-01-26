<x-layouts.app title="Buchungsdetails">
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('organizer.bookings.index') }}" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center">
                    ← Zurück zur Übersicht
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mt-4">Buchungsdetails</h1>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Buchungsinformationen -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $booking->booking_number }}</h2>
                        <p class="text-gray-600">Gebucht am {{ $booking->created_at->format('d.m.Y H:i') }} Uhr</p>
                    </div>
                    <div class="text-right">
                        @if($booking->status === 'pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Ausstehend</span>
                        @elseif($booking->status === 'confirmed')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Bestätigt</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Storniert</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ ucfirst($booking->status) }}</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Event -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Event</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->event->title }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->event->start_date->format('d.m.Y H:i') }} Uhr</p>
                        <p class="text-sm text-gray-600">{{ $booking->event->venue_name }}, {{ $booking->event->venue_city }}</p>
                    </div>

                    <!-- Kunde -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Kunde</h3>
                        <p class="text-lg font-semibold text-gray-900">{{ $booking->customer_name }}</p>
                        <p class="text-sm text-gray-600">{{ $booking->customer_email }}</p>
                        @if($booking->customer_phone)
                            <p class="text-sm text-gray-600">{{ $booking->customer_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tickets -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Tickets</h2>

                @if($booking->needsPersonalization())
                    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">
                                    Personalisierung erforderlich - Der Kunde muss die Tickets noch personalisieren, bevor sie versendet werden können.
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($booking->tickets_personalized)
                    <div class="bg-green-50 border border-green-300 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-green-800">
                                    Tickets personalisiert am {{ $booking->tickets_personalized_at->format('d.m.Y H:i') }} Uhr
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    @foreach($booking->items as $item)
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item->ticketType->name }}</h3>
                                    <p class="text-sm text-gray-600">Ticket-Nr: {{ $item->ticket_number }}</p>
                                    @if($item->attendee_name)
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <p class="text-sm text-blue-600">
                                                <span class="font-medium">Teilnehmer:</span> {{ $item->attendee_name }}
                                            </p>
                                            @if($item->attendee_email)
                                                <p class="text-xs text-gray-500">{{ $item->attendee_email }}</p>
                                            @endif
                                        </div>
                                    @endif
                                    @if($item->checked_in)
                                        <div class="mt-2 pt-2 border-t border-gray-100">
                                            <p class="text-sm text-green-600">
                                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Eingecheckt am {{ $item->checked_in_at->format('d.m.Y H:i') }} Uhr
                                            </p>
                                            @if($booking->event->end_date->isPast())
                                                <p class="text-xs text-purple-600 mt-1">
                                                    ✓ Zertifikat verfügbar
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">{{ number_format($item->price, 2, ',', '.') }} €</p>
                                    <p class="text-sm text-gray-600">Anzahl: {{ $item->quantity }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kosten -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Kosten</h2>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Zwischensumme</span>
                        <span class="font-medium">{{ number_format($booking->subtotal, 2, ',', '.') }} €</span>
                    </div>
                    @if($booking->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Rabatt @if($booking->discountCode) ({{ $booking->discountCode->code }}) @endif</span>
                            <span>-{{ number_format($booking->discount, 2, ',', '.') }} €</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Gesamt</span>
                        <span>{{ number_format($booking->total, 2, ',', '.') }} €</span>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-600">Zahlungsmethode</span>
                        @if($booking->payment_method === 'paypal')
                            <span class="inline-flex items-center">
                                <svg class="w-16 h-4" viewBox="0 0 124 33" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M46.211 6.749h-6.839a.95.95 0 0 0-.939.802l-2.766 17.537a.57.57 0 0 0 .564.658h3.265a.95.95 0 0 0 .939-.803l.746-4.73a.95.95 0 0 1 .938-.803h2.165c4.505 0 7.105-2.18 7.784-6.5.306-1.89.013-3.375-.872-4.415-.972-1.142-2.696-1.746-4.985-1.746zM47 13.154c-.374 2.454-2.249 2.454-4.062 2.454h-1.032l.724-4.583a.57.57 0 0 1 .563-.481h.473c1.235 0 2.4 0 3.002.704.359.42.469 1.044.332 1.906zM66.654 13.075h-3.275a.57.57 0 0 0-.563.481l-.145.916-.229-.332c-.709-1.029-2.29-1.373-3.868-1.373-3.619 0-6.71 2.741-7.312 6.586-.313 1.918.132 3.752 1.22 5.031.998 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .562.66h2.95a.95.95 0 0 0 .939-.803l1.77-11.209a.568.568 0 0 0-.561-.658zm-4.565 6.374c-.316 1.871-1.801 3.127-3.695 3.127-.951 0-1.711-.305-2.199-.883-.484-.574-.668-1.391-.514-2.301.295-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.499.589.697 1.411.554 2.317zM84.096 13.075h-3.291a.954.954 0 0 0-.787.417l-4.539 6.686-1.924-6.425a.953.953 0 0 0-.912-.678h-3.234a.57.57 0 0 0-.541.754l3.625 10.638-3.408 4.811a.57.57 0 0 0 .465.9h3.287a.949.949 0 0 0 .781-.408l10.946-15.8a.57.57 0 0 0-.468-.895z" fill="#253B80"/>
                                    <path d="M94.992 6.749h-6.84a.95.95 0 0 0-.938.802l-2.766 17.537a.569.569 0 0 0 .562.658h3.51a.665.665 0 0 0 .656-.562l.785-4.971a.95.95 0 0 1 .938-.803h2.164c4.506 0 7.105-2.18 7.785-6.5.307-1.89.012-3.375-.873-4.415-.971-1.142-2.694-1.746-4.983-1.746zm.789 6.405c-.373 2.454-2.248 2.454-4.062 2.454h-1.031l.725-4.583a.568.568 0 0 1 .562-.481h.473c1.234 0 2.4 0 3.002.704.359.42.468 1.044.331 1.906zM115.434 13.075h-3.273a.567.567 0 0 0-.562.481l-.145.916-.23-.332c-.709-1.029-2.289-1.373-3.867-1.373-3.619 0-6.709 2.741-7.311 6.586-.312 1.918.131 3.752 1.219 5.031 1 1.176 2.426 1.666 4.125 1.666 2.916 0 4.533-1.875 4.533-1.875l-.146.91a.57.57 0 0 0 .564.66h2.949a.95.95 0 0 0 .938-.803l1.771-11.209a.571.571 0 0 0-.565-.658zm-4.565 6.374c-.314 1.871-1.801 3.127-3.695 3.127-.949 0-1.711-.305-2.199-.883-.484-.574-.666-1.391-.514-2.301.297-1.855 1.805-3.152 3.67-3.152.93 0 1.686.309 2.184.892.501.589.699 1.411.554 2.317zM119.295 7.23l-2.807 17.858a.569.569 0 0 0 .562.658h2.822c.469 0 .867-.34.939-.803l2.768-17.536a.57.57 0 0 0-.562-.659h-3.16a.571.571 0 0 0-.562.482z" fill="#179BD7"/>
                                    <path d="M7.266 29.154l.523-3.322-1.165-.027H1.061L4.927 1.292a.316.316 0 0 1 .314-.268h9.38c3.114 0 5.263.648 6.385 1.927.526.6.861 1.227 1.023 1.917.17.724.173 1.589.007 2.644l-.012.077v.676l.526.298a3.69 3.69 0 0 1 1.065.812c.45.513.741 1.165.864 1.938.127.795.085 1.741-.123 2.812-.24 1.232-.628 2.305-1.152 3.183a6.547 6.547 0 0 1-1.825 2c-.696.494-1.523.869-2.458 1.109-.906.236-1.939.355-3.072.355h-.73c-.522 0-1.029.188-1.427.525a2.21 2.21 0 0 0-.744 1.328l-.055.299-.924 5.855-.042.215c-.011.068-.03.102-.058.125a.155.155 0 0 1-.096.035H7.266z" fill="#253B80"/>
                                    <path d="M23.048 7.667c-.028.179-.06.362-.096.55-1.237 6.351-5.469 8.545-10.874 8.545H9.326c-.661 0-1.218.48-1.321 1.132L6.596 26.83l-.399 2.533a.704.704 0 0 0 .695.814h4.881c.578 0 1.069-.42 1.16-.99l.048-.248.919-5.832.059-.32c.09-.572.582-.992 1.16-.992h.73c4.729 0 8.431-1.92 9.513-7.476.452-2.321.218-4.259-.978-5.622a4.667 4.667 0 0 0-1.336-1.03z" fill="#179BD7"/>
                                    <path d="M21.754 7.151a9.757 9.757 0 0 0-1.203-.267 15.284 15.284 0 0 0-2.426-.177h-7.352a1.172 1.172 0 0 0-1.159.992L8.05 17.605l-.045.289a1.336 1.336 0 0 1 1.321-1.132h2.752c5.405 0 9.637-2.195 10.874-8.545.037-.188.068-.371.096-.55a6.594 6.594 0 0 0-1.017-.429 9.045 9.045 0 0 0-.277-.087z" fill="#222D65"/>
                                    <path d="M9.614 7.699a1.169 1.169 0 0 1 1.159-.991h7.352c.871 0 1.684.057 2.426.177a9.757 9.757 0 0 1 1.481.353c.365.121.704.264 1.017.429.368-2.347-.003-3.945-1.272-5.392C20.378.682 17.853 0 14.622 0h-9.38c-.66 0-1.223.48-1.325 1.133L.01 25.898a.806.806 0 0 0 .795.932h5.791l1.454-9.225 1.564-9.906z" fill="#253B80"/>
                                </svg>
                            </span>
                        @elseif($booking->payment_method === 'invoice')
                            <span class="inline-flex items-center text-gray-700">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Rechnung
                            </span>
                        @else
                            <span class="text-gray-700">{{ $booking->payment_method ?? 'Nicht angegeben' }}</span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Zahlungsstatus</span>
                        @if($booking->payment_status === 'paid')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">Bezahlt</span>
                        @elseif($booking->payment_status === 'pending')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Ausstehend</span>
                        @elseif($booking->payment_status === 'refunded')
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Erstattet</span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">Fehlgeschlagen</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Aktionen -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Aktionen</h2>
                <div class="flex flex-wrap gap-4">
                    <!-- Status ändern -->
                    @if($booking->status !== 'cancelled')
                        <form action="{{ route('organizer.bookings.update-status', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 shadow-sm">
                                <option value="">Status ändern...</option>
                                <option value="pending" {{ $booking->status === 'pending' ? 'disabled' : '' }}>Ausstehend</option>
                                <option value="confirmed" {{ $booking->status === 'confirmed' ? 'disabled' : '' }}>Bestätigen</option>
                                <option value="completed" {{ $booking->status === 'completed' ? 'disabled' : '' }}>Abschließen</option>
                                <option value="cancelled" {{ $booking->status === 'cancelled' ? 'disabled' : '' }}>Stornieren</option>
                            </select>
                        </form>

                        <!-- Zahlungsstatus ändern -->
                        <form action="{{ route('organizer.bookings.update-payment', $booking) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <select name="payment_status" onchange="this.form.submit()" class="rounded-lg border-gray-300 shadow-sm">
                                <option value="">Zahlungsstatus ändern...</option>
                                <option value="pending" {{ $booking->payment_status === 'pending' ? 'disabled' : '' }}>Ausstehend</option>
                                <option value="paid" {{ $booking->payment_status === 'paid' ? 'disabled' : '' }}>Bezahlt</option>
                                <option value="refunded" {{ $booking->payment_status === 'refunded' ? 'disabled' : '' }}>Erstattet</option>
                                <option value="failed" {{ $booking->payment_status === 'failed' ? 'disabled' : '' }}>Fehlgeschlagen</option>
                            </select>
                        </form>
                    @endif

                    <!-- Buchung ansehen (Kundenansicht) -->
                    <a href="{{ route('bookings.show', $booking->booking_number) }}" target="_blank"
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        Kundenansicht öffnen
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

