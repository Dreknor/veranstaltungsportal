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
    <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
        {{-- Mobil: Liste, ab sm: Tabelle --}}
        <ul class="sm:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($rows as [$trigger, $mail, $access])
                <li class="p-3 text-sm">
                    <p class="font-medium text-gray-900 dark:text-gray-100">{{ $trigger }}</p>
                    <p class="text-gray-700 dark:text-gray-300">→ {{ $mail }}</p>
                    @if($access)
                        <p class="mt-1 text-xs font-medium text-green-700 dark:text-green-400">🎫 inkl. Tickets bzw. Online-Zugangsdaten</p>
                    @endif
                </li>
            @endforeach
        </ul>
        <table class="hidden sm:table min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">Wann?</th>
                    <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-700 dark:text-gray-300">E-Mail an Teilnehmende</th>
                    <th scope="col" class="px-3 py-2 text-center font-semibold text-gray-700 dark:text-gray-300">Tickets / Zugang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-900">
                @foreach($rows as [$trigger, $mail, $access])
                    <tr>
                        <td class="px-3 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $trigger }}</td>
                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300">{{ $mail }}</td>
                        <td class="px-3 py-2 text-center">{!! $access ? '<span class="text-green-600" title="enthält Tickets bzw. Zugangsdaten">✓</span>' : '<span class="text-gray-400">–</span>' !!}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="mt-3 text-sm text-gray-700 dark:text-gray-300">
        <strong>Online-Zugangsdaten</strong> (Link und Code) sehen ausschließlich Buchungen, die <em>bestätigt und bezahlt</em> sind
        (kostenfreie Anmeldungen nach Freigabe, extern fakturierte Buchungen gelten als bezahlt) –
        in der Bestätigungs-E-Mail, auf der Buchungsseite und in den Erinnerungen.
        Jede versendete E-Mail sehen Sie im <strong>E-Mail-Verlauf</strong> der Buchung und können sie dort erneut senden.
    </p>
</div>
