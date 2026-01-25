<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Featured Event Details
            </h2>
            <a href="{{ route('admin.featured-events.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Event Info -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Event Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event</label>
                                <a href="{{ route('events.show', $fee->event->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    {{ $fee->event->title }}
                                    <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Veranstalter</label>
                                <div class="text-gray-900 dark:text-white">{{ $fee->event->organizer->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $fee->event->organizer->email }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Startdatum</label>
                                    <div class="text-gray-900 dark:text-white">{{ $fee->event->starts_at->format('d.m.Y H:i') }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Event Status</label>
                                    <div>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $fee->event->published ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                            {{ $fee->event->published ? 'Veröffentlicht' : 'Entwurf' }}
                                        </span>
                                        @if($fee->event->featured)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 ml-2">
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Details -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gebühren Details</h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Betrag</label>
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($fee->fee_amount, 2, ',', '.') }} €</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zeitraum</label>
                                    <div class="text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($fee->featured_start_date)->diffInDays(\Carbon\Carbon::parse($fee->featured_end_date)) }} Tag(e)
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($fee->featured_start_date)->format('d.m.Y') }} - {{ \Carbon\Carbon::parse($fee->featured_end_date)->format('d.m.Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Erstellt am</label>
                                    <div class="text-gray-900 dark:text-white">{{ $fee->created_at->format('d.m.Y H:i') }}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Läuft ab am</label>
                                    <div class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($fee->featured_end_date)->format('d.m.Y') }}</div>
                                    @if(\Carbon\Carbon::parse($fee->featured_end_date)->isPast())
                                        <span class="text-xs text-red-600 dark:text-red-400">Abgelaufen</span>
                                    @elseif(\Carbon\Carbon::parse($fee->featured_end_date)->diffInDays() <= 3)
                                        <span class="text-xs text-orange-600 dark:text-orange-400">Läuft in {{ \Carbon\Carbon::parse($fee->featured_end_date)->diffInDays() }} Tag(en) ab</span>
                                    @else
                                        <span class="text-xs text-green-600 dark:text-green-400">Noch {{ \Carbon\Carbon::parse($fee->featured_end_date)->diffInDays() }} Tag(e)</span>
                                    @endif
                                </div>
                            </div>

                            @if($fee->paid_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bezahlt am</label>
                                    <div class="text-gray-900 dark:text-white">{{ $fee->paid_at->format('d.m.Y H:i') }}</div>
                                </div>
                            @endif

                            @if($fee->expiry_notified_at)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ablauf-Benachrichtigung gesendet</label>
                                    <div class="text-gray-900 dark:text-white">{{ $fee->expiry_notified_at->format('d.m.Y H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Extend Period -->
                    @if($fee->payment_status === 'paid')
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Zeitraum verlängern</h3>

                            <form method="POST" action="{{ route('admin.featured-events.extend', $fee) }}">
                                @csrf
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="duration_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Zeitraum-Typ</label>
                                        <select name="duration_type" id="duration_type" class="input" required>
                                            <option value="daily">Täglich</option>
                                            <option value="weekly">Wöchentlich</option>
                                            <option value="monthly">Monatlich</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="duration_count" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Anzahl</label>
                                        <input type="number" name="duration_count" id="duration_count" min="1" max="365" value="1" class="input" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn-primary mt-4" onclick="return confirm('Sind Sie sicher, dass Sie den Zeitraum verlängern möchten?')">
                                    <i class="fas fa-calendar-plus mr-2"></i>
                                    Zeitraum verlängern
                                </button>
                            </form>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Zahlungsstatus</h3>

                        <div class="mb-4">
                            <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full
                                @if($fee->payment_status === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @elseif($fee->payment_status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @elseif($fee->payment_status === 'failed') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                @endif">
                                {{ ucfirst($fee->payment_status) }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('admin.featured-events.update-status', $fee) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status ändern</label>
                                <select name="payment_status" id="payment_status" class="input">
                                    <option value="pending" {{ $fee->payment_status === 'pending' ? 'selected' : '' }}>Ausstehend</option>
                                    <option value="paid" {{ $fee->payment_status === 'paid' ? 'selected' : '' }}>Bezahlt</option>
                                    <option value="failed" {{ $fee->payment_status === 'failed' ? 'selected' : '' }}>Fehlgeschlagen</option>
                                    <option value="refunded" {{ $fee->payment_status === 'refunded' ? 'selected' : '' }}>Erstattet</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-primary w-full" onclick="return confirm('Sind Sie sicher, dass Sie den Status ändern möchten?')">
                                Status aktualisieren
                            </button>
                        </form>
                    </div>

                    <!-- Actions Card -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Aktionen</h3>

                        <div class="space-y-3">
                            @if($fee->payment_status === 'pending')
                                <form method="POST" action="{{ route('admin.featured-events.send-reminder', $fee) }}">
                                    @csrf
                                    <button type="submit" class="btn-secondary w-full">
                                        <i class="fas fa-envelope mr-2"></i>
                                        Zahlungserinnerung senden
                                    </button>
                                </form>
                            @endif

                            @if($fee->payment_status !== 'paid')
                                <form method="POST" action="{{ route('admin.featured-events.cancel', $fee) }}">
                                    @csrf
                                    <button type="submit" class="btn-danger w-full" onclick="return confirm('Sind Sie sicher, dass Sie diese Gebühr stornieren möchten?')">
                                        <i class="fas fa-times-circle mr-2"></i>
                                        Gebühr stornieren
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('organizer.dashboard') }}" class="btn-secondary w-full block text-center">
                                <i class="fas fa-user mr-2"></i>
                                Veranstalter-Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

