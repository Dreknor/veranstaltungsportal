{{--
    Ticketversand bei kostenpflichtigen Buchungen (nur bei externer Rechnungsstellung der Organisation).
    Erwartet: $organization, $value (bool)
--}}
@if($organization?->hasExternalInvoicing())
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Ticketversand bei kostenpflichtigen Buchungen (externe Rechnungsstellung)
        </label>
        <div class="space-y-2">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="radio" name="tickets_before_invoice" value="0" @checked(!$value)
                       class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <div>
                    <span class="text-sm font-medium text-gray-900">Nach Rechnungsstellung</span>
                    <p class="text-xs text-gray-500">Tickets und Zugangsdaten gehen raus, sobald Sie die Buchung als fakturiert markieren.</p>
                </div>
            </label>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="radio" name="tickets_before_invoice" value="1" @checked($value)
                       class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <div>
                    <span class="text-sm font-medium text-gray-900">Sofort bei Buchung – Rechnung folgt später</span>
                    <p class="text-xs text-gray-500">Buchungen werden direkt bestätigt und erhalten Tickets bzw. Zugangsdaten. Die Rechnung stellen Sie extern, auch nach der Veranstaltung. Offene Rechnungen sehen Sie unter „Rechnungsdaten“.</p>
                </div>
            </label>
        </div>
        <p class="text-xs text-gray-500 mt-2">Einzelne Buchungen können Sie in den Buchungsdetails jederzeit vorab freigeben.</p>
    </div>
@endif
