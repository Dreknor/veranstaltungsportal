<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Aufbewahrung personenbezogener Buchungsdaten (DSGVO Art. 5 Abs. 1 lit. e)
    |--------------------------------------------------------------------------
    |
    | Kontaktdaten (E-Mail, Telefon), Teilnehmerdaten und Zusatzangaben werden
    | nach Ablauf dieser Frist (in Monaten nach Veranstaltungsende) anonymisiert.
    |
    */
    'booking_retention_months' => (int) env('BOOKING_RETENTION_MONTHS', 24),

    /*
    |--------------------------------------------------------------------------
    | Steuerliche Aufbewahrung (§ 147 AO, § 257 HGB)
    |--------------------------------------------------------------------------
    |
    | Name und Rechnungsanschrift kostenpflichtiger Buchungen werden bis zum
    | Ablauf dieser Frist (in Jahren nach Buchungsdatum bzw. Rechnungsdatum)
    | aufbewahrt und erst danach anonymisiert.
    |
    */
    'invoice_retention_years' => (int) env('INVOICE_RETENTION_YEARS', 10),

    /*
    | Wartelisten-Einträge vergangener Veranstaltungen (in Monaten nach Veranstaltungsende)
    */
    'waitlist_retention_months' => (int) env('WAITLIST_RETENTION_MONTHS', 3),
];
