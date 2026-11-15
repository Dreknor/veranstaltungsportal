{{--
    Ticketversand bei kostenpflichtigen Buchungen (nur bei externer Rechnungsstellung der Organisation).
    Erwartet: $organization, $value (bool). Abstand nach oben setzt der Aufrufer.
--}}
@if ($organization?->hasExternalInvoicing())
    <fieldset class="m-0 min-w-0 border-0 p-0">
        <x-ui.label as="legend">Ticketversand bei kostenpflichtigen Buchungen (externe Rechnungsstellung)</x-ui.label>
        <x-ui.radio name="tickets_before_invoice" value="0" :checked="!$value" :old="false"
            label="Nach Rechnungsstellung"
            hint="Tickets und Zugangsdaten gehen raus, sobald Sie die Buchung als fakturiert markieren." />
        <x-ui.radio name="tickets_before_invoice" value="1" :checked="$value" :old="false"
            label="Sofort bei Buchung – Rechnung folgt später"
            hint="Buchungen werden direkt bestätigt und erhalten Tickets bzw. Zugangsdaten. Die Rechnung stellen Sie extern, auch nach der Veranstaltung. Offene Rechnungen sehen Sie unter „Rechnungsdaten“." />
        <x-ui.hint>Einzelne Buchungen können Sie in den Buchungsdetails jederzeit vorab freigeben.</x-ui.hint>
    </fieldset>
@endif
