{{--
    Kommunikations-Fahrplan: Welche E-Mail geht wann an wen?
    Quelle der Wahrheit ist App\Services\BookingWorkflowService – bei Änderungen dort bitte hier nachziehen.
--}}
@php
    $rows = [
        ['Buchung eingegangen – kostenfrei, automatische Bestätigung', 'Buchungsbestätigung', true],
        ['Buchung eingegangen – kostenfrei mit Freigabe', '„Anmeldung eingegangen“ (Sie erhalten eine Freigabe-Anfrage)', false],
        ['Buchung eingegangen – kostenpflichtig (Rechnung)', 'Buchungsbestätigung mit Rechnung und Zahlungsinformationen', false],
        ['Buchung eingegangen – PayPal', 'Keine E-Mail bis zur Zahlung. Nicht abgeschlossene Zahlungen werden nach 48 h automatisch storniert (mit Info).', false],
        ['Sie geben eine Anmeldung frei', 'Buchungsbestätigung', true],
        ['Sie lehnen eine Anmeldung ab', 'Absage (optional mit Begründung)', false],
        ['Zahlung verbucht bzw. extern fakturiert', 'Zahlungsbestätigung – die Buchung wird dabei automatisch bestätigt', true],
        ['Externe Rechnung, „Tickets sofort – Rechnung später“ (Event-Einstellung oder Einzelfreigabe)', 'Buchungsbestätigung sofort; die Rechnung stellen Sie später, auch nach der Veranstaltung', true],
        ['Mehrere Plätze gebucht', 'Aufforderung, die Teilnehmenden einzutragen; danach personalisierte Tickets', true],
        ['Teilnehmende mit eigener E-Mail-Adresse eingetragen', 'Jede Person erhält ihr eigenes Ticket bzw. ihre Zugangsdaten', true],
        ['24 h und 3 h vor Beginn (bei Mehrfachterminen vor jedem Termin)', 'Erinnerung an Bucher und eingetragene Teilnehmende', true],
        ['Termin, Ort oder Titel geändert', 'Änderungsinfo an alle bestätigten Buchungen', false],
        ['Online-Link oder Zugangscode geändert', 'Neue Zugangsdaten an alle Buchungen mit freigeschaltetem Zugang', true],
        ['Storno durch Teilnehmende', 'Stornobestätigung (Sie werden benachrichtigt)', false],
        ['Storno durch Sie', 'Stornobestätigung mit Grund (abwählbar)', false],
        ['Absage der Veranstaltung', 'Genau eine Absage-Mail an alle aktiven Buchungen – Erstattungen veranlassen Sie', false],
        ['Platz wird frei und jemand steht auf der Warteliste', 'Persönlicher Buchungslink; der Platz ist 48 h reserviert, danach rückt die nächste Person nach', false],
        ['Nach der Veranstaltung', 'Dank, Teilnahmebescheinigung (eingecheckte Personen) und Bitte um Feedback', false],
        ['Zahlung erstattet / fehlgeschlagen', 'Information zum Zahlungsstatus', false],
    ];
@endphp

<div {{ $attributes ?? '' }}>
    {{-- Mobil: Liste, ab sm: Tabelle --}}
    <ul class="m-0 list-none divide-y divide-line rounded-box border border-line p-0 sm:hidden">
        @foreach ($rows as [$trigger, $mail, $access])
            <li class="flex flex-col gap-1 p-4 text-small">
                <p class="m-0 font-medium text-ink">{{ $trigger }}</p>
                <p class="m-0 flex items-start gap-2 text-ink-soft">
                    <x-icon.arrow-right class="mt-0.5 h-4 w-4 flex-none" />
                    <span>{{ $mail }}</span>
                </p>
                @if ($access)
                    <p class="m-0 flex items-center gap-2 font-medium text-success">
                        <x-icon.ticket class="h-4 w-4 flex-none" />
                        inkl. Tickets bzw. Online-Zugangsdaten
                    </p>
                @endif
            </li>
        @endforeach
    </ul>
    <x-ui.table label="Kommunikations-Fahrplan" class="hidden sm:block">
        <thead>
            <tr>
                <th scope="col">Wann?</th>
                <th scope="col">E-Mail an Teilnehmende</th>
                <th scope="col" class="text-center">Tickets / Zugang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as [$trigger, $mail, $access])
                <tr>
                    <th scope="row">{{ $trigger }}</th>
                    <td>{{ $mail }}</td>
                    <td class="text-center">
                        @if ($access)
                            <span class="inline-flex text-success" title="enthält Tickets bzw. Zugangsdaten">
                                <x-icon.check class="h-5 w-5" />
                                <span class="sr-only">ja, enthält Tickets bzw. Zugangsdaten</span>
                            </span>
                        @else
                            <span class="text-muted">–<span class="sr-only"> nein</span></span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-ui.table>
    <p class="mt-3 mb-0 max-w-prose text-small text-ink-soft">
        <strong class="font-semibold">Online-Zugangsdaten</strong> (Link und Code) sehen ausschließlich Buchungen, die
        bestätigt und bezahlt sind
        (kostenfreie Anmeldungen nach Freigabe, extern fakturierte Buchungen gelten als bezahlt) –
        in der Bestätigungs-E-Mail, auf der Buchungsseite und in den Erinnerungen.
        Jede versendete E-Mail sehen Sie im <strong class="font-semibold">E-Mail-Verlauf</strong> der Buchung und können sie dort erneut senden.
    </p>
</div>
