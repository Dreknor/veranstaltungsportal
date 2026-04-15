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

    <!-- Flash Messages -->
    @if(session('status'))
        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-xl mr-3"></i>
                <p class="font-medium">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg" role="alert">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle text-xl mr-3"></i>
                <p class="font-medium">Fehler aufgetreten:</p>
            </div>
            <ul class="list-disc list-inside ml-8">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-ticket-alt text-blue-600 dark:text-blue-400 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">Tickets gesamt</p>
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

    <!-- Ticket List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Ticket-Liste</h2>

                <!-- Bulk-Action-Toolbar (nur sichtbar wenn etwas ausgewählt) -->
                <div id="bulkToolbar" class="hidden items-center gap-3">
                    <span id="selectedCount" class="text-sm text-gray-600 dark:text-gray-400 font-medium"></span>
                    <form id="bulkForm"
                          action="{{ route('organizer.check-in.bulk', $event) }}"
                          method="POST">
                        @csrf
                        <div id="bulkItemInputs"></div>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                            <i class="fas fa-check-double mr-2"></i> Ausgewählte einchecken
                        </button>
                    </form>
                </div>
            </div>

            <!-- Suche & Filter -->
            <div class="flex flex-col sm:flex-row gap-3 mb-4">
                <div class="flex-1">
                    <input type="text"
                           id="searchInput"
                           placeholder="Nach Name, E-Mail, Ticket-Nr. oder Buchungsnr. suchen..."
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                </div>
                <div>
                    <select id="statusFilter"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100">
                        <option value="">Alle</option>
                        <option value="pending">Ausstehend</option>
                        <option value="checked_in">Eingecheckt</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="ticketsTable">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 w-10">
                                <!-- Alle ausstehenden auswählen -->
                                <input type="checkbox" id="selectAllPending"
                                       class="rounded border-gray-300 dark:border-gray-500 text-blue-600 focus:ring-blue-500"
                                       title="Alle ausstehenden auswählen">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ticket-Nr.
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Name / Organisation
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                E-Mail
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ticket-Typ
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Buchungs-Nr.
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Check-In Zeit
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Aktionen
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($items as $item)
                            @php
                                $name  = $item->attendee_name  ?: $item->booking->customer_name;
                                $email = $item->attendee_email ?: $item->booking->customer_email;
                                $org   = $item->attendee_organization ?: $item->booking->customer_organization;
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 ticket-row"
                                data-checked="{{ $item->checked_in ? '1' : '0' }}"
                                data-item-id="{{ $item->id }}">
                                <!-- Checkbox (nur für noch nicht eingecheckte Tickets) -->
                                <td class="px-4 py-4">
                                    @if(!$item->checked_in)
                                        <input type="checkbox"
                                               class="item-checkbox rounded border-gray-300 dark:border-gray-500 text-blue-600 focus:ring-blue-500"
                                               value="{{ $item->id }}">
                                    @endif
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($item->checked_in)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <i class="fas fa-check-circle mr-1"></i> Eingecheckt
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            <i class="fas fa-clock mr-1"></i> Ausstehend
                                        </span>
                                    @endif
                                </td>

                                <!-- Ticket-Nummer -->
                                <td class="px-4 py-4 whitespace-nowrap text-xs font-mono text-gray-600 dark:text-gray-400">
                                    {{ $item->ticket_number }}
                                </td>

                                <!-- Name / Organisation -->
                                <td class="px-4 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $name }}</div>
                                    @if($org)
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-0.5">{{ $org }}</div>
                                    @endif
                                </td>

                                <!-- E-Mail -->
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $email }}
                                </td>

                                <!-- Ticket-Typ -->
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $item->ticketType->name ?? '—' }}
                                </td>

                                <!-- Buchungs-Nr. -->
                                <td class="px-4 py-4 whitespace-nowrap text-xs font-mono text-gray-500 dark:text-gray-400">
                                    {{ $item->booking->booking_number }}
                                </td>

                                <!-- Check-In Zeit -->
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    @if($item->checked_in && $item->checked_in_at)
                                        {{ $item->checked_in_at->format('d.m.Y H:i') }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <!-- Aktionen -->
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($item->checked_in)
                                        <form action="{{ route('organizer.check-in.item.undo', [$event, $item]) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Check-in für dieses Ticket wirklich rückgängig machen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 text-xs">
                                                <i class="fas fa-undo mr-1"></i> Rückgängig
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('organizer.check-in.item.store', [$event, $item]) }}"
                                              method="POST"
                                              class="inline check-in-form">
                                            @csrf
                                            <button type="submit"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 check-in-btn text-xs">
                                                <i class="fas fa-check mr-1"></i> Einchecken
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
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

        // ─── Suche ───────────────────────────────────────────────────────────
        function applyFilters() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const statusVal = document.getElementById('statusFilter').value; // '' | 'pending' | 'checked_in'

            document.querySelectorAll('#ticketsTable tbody .ticket-row').forEach(row => {
                const text      = row.textContent.toLowerCase();
                const isChecked = row.dataset.checked === '1';

                const matchesSearch = !search || text.includes(search);
                const matchesStatus =
                    statusVal === '' ||
                    (statusVal === 'pending'    && !isChecked) ||
                    (statusVal === 'checked_in' &&  isChecked);

                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }

        document.getElementById('searchInput').addEventListener('keyup', applyFilters);
        document.getElementById('statusFilter').addEventListener('change', applyFilters);

        // ─── Checkbox-Logik ──────────────────────────────────────────────────
        const selectAllCheckbox = document.getElementById('selectAllPending');
        const bulkToolbar       = document.getElementById('bulkToolbar');
        const bulkItemInputs    = document.getElementById('bulkItemInputs');
        const selectedCountEl   = document.getElementById('selectedCount');

        function updateBulkToolbar() {
            const checked = document.querySelectorAll('.item-checkbox:checked');
            if (checked.length > 0) {
                bulkToolbar.classList.remove('hidden');
                bulkToolbar.classList.add('flex');
                selectedCountEl.textContent = checked.length + ' Ticket(s) ausgewählt';

                // Versteckte Inputs für das Bulk-Formular aufbauen
                bulkItemInputs.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = 'item_ids[]';
                    input.value = cb.value;
                    bulkItemInputs.appendChild(input);
                });
            } else {
                bulkToolbar.classList.add('hidden');
                bulkToolbar.classList.remove('flex');
            }

            // Haupt-Checkbox-Status aktualisieren
            const allPendingBoxes = document.querySelectorAll('.item-checkbox');
            selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < allPendingBoxes.length;
            selectAllCheckbox.checked = allPendingBoxes.length > 0 && checked.length === allPendingBoxes.length;
        }

        // "Alle ausstehenden" Checkbox
        selectAllCheckbox.addEventListener('change', function () {
            // Nur sichtbare, noch nicht eingecheckte Zeilen
            document.querySelectorAll('.ticket-row').forEach(row => {
                if (row.style.display === 'none') return;
                const cb = row.querySelector('.item-checkbox');
                if (cb) cb.checked = this.checked;
            });
            updateBulkToolbar();
        });

        // Einzelne Checkboxen
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('item-checkbox')) {
                updateBulkToolbar();
            }
        });

        // ─── Einzel-Check-In Formulare ────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.check-in-form').forEach(form => {
                form.addEventListener('submit', function () {
                    const btn = this.querySelector('.check-in-btn');
                    btn.disabled  = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> …';
                    setTimeout(() => {
                        btn.disabled  = false;
                        btn.innerHTML = '<i class="fas fa-check mr-1"></i> Einchecken';
                    }, 4000);
                });
            });
        });

        // ─── QR-Scanner ──────────────────────────────────────────────────────
        function openQrScanner() {
            document.getElementById('qrScannerModal').classList.remove('hidden');

            html5QrCode = new Html5Qrcode("qrReader");
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
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
                }).catch(err => console.error('Unable to stop scanning', err));
            } else {
                document.getElementById('qrScannerModal').classList.add('hidden');
            }
        }

        function onScanSuccess(decodedText) {
            html5QrCode.pause();

            fetch('{{ route('organizer.check-in.scan', $event) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ booking_number: decodedText })
            })
            .then(r => r.json())
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
                                <p class="text-xs">${data.booking.tickets_count} Ticket(s) eingecheckt</p>
                            </div>
                        </div>`;
                    setTimeout(() => location.reload(), 2000);
                } else {
                    resultDiv.className = 'mb-4 p-4 rounded-lg bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                    resultDiv.innerHTML = `
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                            <div>
                                <p class="font-semibold">Fehler</p>
                                <p class="text-sm">${data.message}</p>
                            </div>
                        </div>`;
                    setTimeout(() => {
                        html5QrCode.resume();
                        resultDiv.classList.add('hidden');
                    }, 3000);
                }
            })
            .catch(() => {
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

        function onScanError() { /* ignoriert häufige Scan-Fehler */ }
    </script>
    @endpush
</x-layouts.app>

