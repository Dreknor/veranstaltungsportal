<x-layouts.app title="Check-in">
    <x-ui.page-header title="Check-in" :back-href="route('organizer.events.index')" back-label="Zu den Veranstaltungen"
        subtitle="{{ $event->title }} · {{ \App\Support\Ui::date($event->start_date) }}, {{ $event->start_date->format('H:i') }} Uhr">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.check-in.export', $event)">
                <x-slot:icon><x-icon.download /></x-slot:icon>
                Export
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($dates->isNotEmpty())
        {{-- Terminauswahl (Veranstaltung mit mehreren Terminen) --}}
        <x-ui.tabs label="Check-in für Termin" class="mb-8">
            @foreach ($dates as $date)
                <x-ui.tab :href="route('organizer.check-in.index', [$event, 'date' => $date->id])" :active="$selectedDate?->id === $date->id" class="tabular-nums">
                    {{ $date->start_date->translatedFormat('D d.m.') }} {{ $date->start_date->format('H:i') }}
                    @if ($date->start_date->isToday())
                        <span class="text-small font-normal text-muted">(heute)</span>
                    @endif
                </x-ui.tab>
            @endforeach
        </x-ui.tabs>
    @endif

    @php
        $progress = $stats['total'] > 0 ? round(($stats['checked_in'] / $stats['total']) * 100, 1) : 0;
    @endphp

    <div class="mb-6 grid gap-4 sm:grid-cols-3">
        <x-ui.stat label="Tickets gesamt" :value="$stats['total']" />
        <x-ui.stat label="Eingecheckt" :value="$stats['checked_in']" />
        <x-ui.stat label="Ausstehend" :value="$stats['pending']" />
    </div>

    <div class="mb-12">
        <div class="mb-2 flex items-center justify-between gap-4 text-small">
            <span class="font-medium text-ink" id="checkin-progress-label">Check-in-Fortschritt</span>
            <span class="text-muted tabular-nums">{{ $progress }} %</span>
        </div>
        <div class="h-2 w-full overflow-hidden rounded-full bg-line" role="progressbar" aria-labelledby="checkin-progress-label"
            aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progress }}">
            <div class="h-2 rounded-full bg-primary" style="width: {{ $progress }}%"></div>
        </div>
    </div>

    {{-- Suche, Filter und QR-Scanner --}}
    <div class="mb-6 flex flex-wrap items-end gap-x-4 gap-y-4">
        <x-ui.field label="Suche" id="searchInput" type="search" class="min-w-0 flex-[3_1_280px]" autocomplete="off"
            placeholder="Name, E-Mail, Ticket- oder Buchungsnummer" />
        <x-ui.select label="Status" id="statusFilter" class="flex-[1_1_160px]"
            :options="['' => 'Alle', 'pending' => 'Ausstehend', 'checked_in' => 'Eingecheckt']" />
        <x-ui.button size="lg" onclick="openQrScanner()" class="flex-[1_1_200px]">QR-Code scannen</x-ui.button>
    </div>

    <section aria-labelledby="ticket-list-heading">
        <div class="mb-4 flex min-h-12 flex-wrap items-center justify-between gap-4">
            <h2 id="ticket-list-heading" class="m-0 text-h2">Tickets</h2>

            {{-- Sammel-Check-in (nur sichtbar, wenn etwas ausgewählt ist) --}}
            <div id="bulkToolbar" class="hidden flex-wrap items-center gap-3">
                <span id="selectedCount" class="text-small font-medium text-ink-soft" aria-live="polite"></span>
                <form id="bulkForm" action="{{ route('organizer.check-in.bulk', $event) }}" method="POST" class="m-0">
                    @csrf
                    @if ($selectedDate)
                        <input type="hidden" name="event_date_id" value="{{ $selectedDate->id }}">
                    @endif
                    <div id="bulkItemInputs"></div>
                    <x-ui.button type="submit">
                        <x-slot:icon><x-icon.check /></x-slot:icon>
                        Ausgewählte einchecken
                    </x-ui.button>
                </form>
            </div>
        </div>

        <x-ui.table label="Tickets" id="ticketsTable">
            <thead>
                <tr>
                    <th scope="col" class="w-12">
                        <input type="checkbox" id="selectAllPending" class="ui-checkbox" title="Alle ausstehenden auswählen"
                            aria-label="Alle ausstehenden auswählen">
                    </th>
                    <th scope="col">Status</th>
                    <th scope="col">Name / Organisation</th>
                    <th scope="col" class="hidden xl:table-cell">E-Mail</th>
                    <th scope="col" class="hidden 2xl:table-cell">Ticketart</th>
                    <th scope="col" class="hidden 2xl:table-cell">Buchungs-Nr.</th>
                    <th scope="col" class="hidden xl:table-cell">Check-in-Zeit</th>
                    <th scope="col" class="actions"><span class="sr-only">Aktionen</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    @php
                        $isChecked = $item->isCheckedInFor($selectedDate);
                        $checkedAt = $selectedDate
                            ? $item->attendances->firstWhere('event_date_id', $selectedDate->id)?->checked_in_at
                            : $item->checked_in_at;
                        $name = $item->attendee_name ?: $item->booking->customer_name;
                        $email = $item->attendee_email ?: $item->booking->customer_email;
                        $org = $item->attendee_organization ?: $item->booking->customer_organization;
                    @endphp
                    <tr class="ticket-row" data-checked="{{ $isChecked ? '1' : '0' }}" data-item-id="{{ $item->id }}">
                        {{-- Auswahl (nur für noch nicht eingecheckte Tickets) --}}
                        <td>
                            @if (!$isChecked)
                                <input type="checkbox" class="item-checkbox ui-checkbox" value="{{ $item->id }}"
                                    aria-label="{{ $name }} auswählen">
                            @endif
                        </td>

                        <td class="whitespace-nowrap">
                            @if ($isChecked)
                                <x-ui.status tone="success">Eingecheckt</x-ui.status>
                            @else
                                <x-ui.status tone="warning">Ausstehend</x-ui.status>
                            @endif
                        </td>

                        <td>
                            <span class="block font-medium">{{ $name }}</span>
                            <span class="block text-small text-muted tabular-nums">{{ $item->ticket_number }}</span>
                            @if ($org)
                                <span class="block text-small text-muted">{{ $org }}</span>
                            @endif
                            <span class="block break-all text-small text-muted xl:hidden">{{ $email }}</span>
                            <span class="block text-small text-muted 2xl:hidden">{{ $item->ticketType->name ?? '' }}</span>
                        </td>

                        <td class="hidden whitespace-nowrap text-muted xl:table-cell">{{ $email }}</td>

                        <td class="hidden whitespace-nowrap 2xl:table-cell">{{ $item->ticketType->name ?? '–' }}</td>

                        <td class="hidden whitespace-nowrap text-small text-muted tabular-nums 2xl:table-cell">{{ $item->booking->booking_number }}</td>

                        <td class="hidden whitespace-nowrap text-muted tabular-nums xl:table-cell">
                            @if ($isChecked && $checkedAt)
                                {{ $checkedAt->format('d.m.Y H:i') }}
                            @else
                                –
                            @endif
                        </td>

                        <td class="actions">
                            @if ($isChecked)
                                <form action="{{ route('organizer.check-in.item.undo', [$event, $item]) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Check-in für dieses Ticket wirklich rückgängig machen?')">
                                    @csrf
                                    @if ($selectedDate)
                                        <input type="hidden" name="event_date_id" value="{{ $selectedDate->id }}">
                                    @endif
                                    @method('DELETE')
                                    <x-ui.button type="submit" variant="link" size="sm">Rückgängig</x-ui.button>
                                </form>
                            @else
                                <form action="{{ route('organizer.check-in.item.store', [$event, $item]) }}" method="POST" class="check-in-form inline">
                                    @csrf
                                    @if ($selectedDate)
                                        <input type="hidden" name="event_date_id" value="{{ $selectedDate->id }}">
                                    @endif
                                    <x-ui.button type="submit" variant="secondary" size="sm" class="check-in-btn">Einchecken</x-ui.button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr class="hover:bg-transparent">
                        <td colspan="8" class="p-0">
                            <x-ui.empty :bordered="false">Keine bestätigten Buchungen vorhanden.</x-ui.empty>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-ui.table>
    </section>

    {{-- QR-Scanner (wird per JavaScript ein- und ausgeblendet) --}}
    <div id="qrScannerModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-ink/50 p-4" role="dialog" aria-modal="true"
        aria-labelledby="qrScannerTitle">
        <div class="w-full max-w-[640px] rounded-box border border-line bg-surface p-6 text-ink shadow-overlay">
            <div class="mb-4 flex items-start justify-between gap-4">
                <h2 id="qrScannerTitle" class="m-0 pt-2 text-h3">QR-Code scannen</h2>
                <button type="button" onclick="closeQrScanner()" aria-label="Scanner schließen"
                    class="-mt-1 -mr-3 inline-flex h-11 w-11 flex-none cursor-pointer items-center justify-center rounded-control text-ink transition-colors duration-150 hover:bg-zone">
                    <x-icon.x class="h-5 w-5" />
                </button>
            </div>

            <div id="qrReader" class="mb-4"></div>

            <div id="scanResult" class="mb-4 hidden rounded-control border p-4" role="status"></div>

            <p class="m-0 text-small text-muted">Halten Sie den QR-Code vor die Kamera.</p>
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
                    btn.innerHTML = 'Wird eingecheckt …';
                    setTimeout(() => {
                        btn.disabled  = false;
                        btn.innerHTML = 'Einchecken';
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
                body: JSON.stringify({ booking_number: decodedText, event_date_id: @json($selectedDate?->id) })
            })
            .then(r => r.json())
            .then(data => {
                const resultDiv = document.getElementById('scanResult');
                resultDiv.classList.remove('hidden');

                if (data.success) {
                    resultDiv.className = 'mb-4 rounded-control border border-success-line bg-success-zone p-4 text-success';
                    resultDiv.innerHTML = `
                        <p class="m-0 font-semibold">${data.message}</p>
                        <p class="m-0 text-small">${data.booking.customer_name}</p>
                        <p class="m-0 text-small">${data.booking.tickets_count} Ticket(s) eingecheckt</p>`;
                    setTimeout(() => location.reload(), 2000);
                } else {
                    resultDiv.className = 'mb-4 rounded-control border border-danger-line bg-danger-zone p-4 text-danger';
                    resultDiv.innerHTML = `
                        <p class="m-0 font-semibold">Fehler</p>
                        <p class="m-0 text-small">${data.message}</p>`;
                    setTimeout(() => {
                        html5QrCode.resume();
                        resultDiv.classList.add('hidden');
                    }, 3000);
                }
            })
            .catch(() => {
                const resultDiv = document.getElementById('scanResult');
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mb-4 rounded-control border border-danger-line bg-danger-zone p-4 text-danger';
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

