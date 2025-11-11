<x-layouts.app>
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('organizer.events.index') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-1"></i> Zurück zu Events
                </a>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Check-In: {{ $event->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ $event->start_date->format('d.m.Y H:i') }} Uhr
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('organizer.check-in.export', $event) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i> Export
                </a>
                <button onclick="openQrScanner()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    <i class="fas fa-qrcode mr-2"></i> QR-Scanner
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Gesamt</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-check text-green-600 dark:text-green-400 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Eingecheckt</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['checked_in'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ausstehend</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $stats['pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Check-In Fortschritt</span>
            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ $stats['total'] > 0 ? round(($stats['checked_in'] / $stats['total']) * 100, 1) : 0 }}%
            </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
            <div class="bg-green-600 h-4 rounded-full transition-all"
                 style="width: {{ $stats['total'] > 0 ? round(($stats['checked_in'] / $stats['total']) * 100, 1) : 0 }}%"></div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Teilnehmerliste</h2>

            <!-- Search -->
            <div class="mb-4">
                <input type="text"
                       id="searchInput"
                       placeholder="Nach Name, E-Mail oder Buchungsnummer suchen..."
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="bookingsTable">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Buchungsnr.
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                E-Mail
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Tickets
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Check-In Zeit
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aktionen
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700" data-booking-id="{{ $booking->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($booking->checked_in)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Eingecheckt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <i class="fas fa-clock mr-1"></i> Ausstehend
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $booking->booking_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $booking->customer_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $booking->customer_email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $booking->items->sum('quantity') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($booking->checked_in)
                                        {{ $booking->checked_in_at->format('d.m.Y H:i') }}
                                        <br>
                                        <span class="text-xs text-gray-400">
                                            {{ $booking->check_in_method === 'qr' ? 'QR-Scan' : 'Manuell' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($booking->checked_in)
                                        <form action="{{ route('organizer.check-in.undo', [$event, $booking]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Check-in wirklich rückgängig machen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400">
                                                <i class="fas fa-undo mr-1"></i> Rückgängig
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('organizer.check-in.store', [$event, $booking]) }}"
                                              method="POST"
                                              class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400">
                                                <i class="fas fa-check mr-1"></i> Einchecken
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    Keine bestätigten Buchungen vorhanden.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div id="qrScannerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">QR-Code Scanner</h3>
                <button onclick="closeQrScanner()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div id="qrReader" class="mb-4"></div>

            <div id="scanResult" class="hidden mb-4 p-4 rounded-lg"></div>

            <div class="text-center text-sm text-gray-600 dark:text-gray-400">
                Halten Sie den QR-Code vor die Kamera
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#bookingsTable tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        function openQrScanner() {
            document.getElementById('qrScannerModal').classList.remove('hidden');

            html5QrCode = new Html5Qrcode("qrReader");
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanError
            ).catch(err => {
                console.error('Unable to start scanning', err);
                alert('Kamera konnte nicht gestartet werden. Bitte überprüfen Sie die Berechtigungen.');
                closeQrScanner();
            });
        }

        function closeQrScanner() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    document.getElementById('qrScannerModal').classList.add('hidden');
                    document.getElementById('scanResult').classList.add('hidden');
                }).catch(err => {
                    console.error('Unable to stop scanning', err);
                });
            } else {
                document.getElementById('qrScannerModal').classList.add('hidden');
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Stop scanning temporarily
            html5QrCode.pause();

            // Send to server
            fetch('{{ route('organizer.check-in.scan', $event) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    booking_number: decodedText
                })
            })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('scanResult');
                resultDiv.classList.remove('hidden');

                if (data.success) {
                    resultDiv.className = 'mb-4 p-4 rounded-lg bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                    resultDiv.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-2xl mr-3"></i>
                            <div>
                                <p class="font-semibold">${data.message}</p>
                                <p class="text-sm">${data.booking.customer_name}</p>
                                <p class="text-xs">${data.booking.tickets_count} Ticket(s)</p>
                            </div>
                        </div>
                    `;

                    // Reload page after 2 seconds
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    resultDiv.className = 'mb-4 p-4 rounded-lg bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                    resultDiv.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                            <div>
                                <p class="font-semibold">Fehler</p>
                                <p class="text-sm">${data.message}</p>
                            </div>
                        </div>
                    `;

                    // Resume scanning after 3 seconds
                    setTimeout(() => {
                        html5QrCode.resume();
                        resultDiv.classList.add('hidden');
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const resultDiv = document.getElementById('scanResult');
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mb-4 p-4 rounded-lg bg-red-100 text-red-800';
                resultDiv.textContent = 'Fehler beim Check-in. Bitte versuchen Sie es erneut.';

                setTimeout(() => {
                    html5QrCode.resume();
                    resultDiv.classList.add('hidden');
                }, 3000);
            });
        }

        function onScanError(errorMessage) {
            // Ignore scan errors (they happen frequently while scanning)
        }
    </script>
    @endpush
</x-layouts.app>

