{{--
    Gemeinsames Stylesheet für alle PDFs (Richtlinie B7). Wird in den <style>-Block jeder
    PDF-Vorlage per @include('pdf.partials.styles') eingebunden.
    Farben ausschließlich aus A2:
      ink #1E3135 · primary #2B4347 · muted #55676A · line #DCE3E2 · zone #F3F6F5
      Erfolg #2C5E1A/#EEF6E0/#B9D98A · Warnung #6B4A0A/#FBF3DF/#E2C88A · Fehler #9B1C1C/#FCEEEE/#E3A9A9
    DomPDF: nur CSS 2.1, Tabellen-Layout, keine Verläufe/Schatten/Flex.
--}}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 9pt;
    line-height: 1.5;
    color: #1E3135;
    background: #FFFFFF;
}
.container,
.ticket-container {
    padding: 20px 25px;
}
.page-break {
    page-break-after: always;
}
.muted {
    color: #55676A;
}
.num {
    text-align: right;
}
.mono {
    font-family: 'DejaVu Sans Mono', monospace;
}

/* Kopf mit Wortmarke */
.brand {
    margin-bottom: 4px;
}
.brand-name {
    font-size: 12pt;
    font-weight: bold;
    color: #1E3135;
    line-height: 1.2;
}
.brand-tag {
    font-size: 8pt;
    color: #55676A;
    line-height: 1.2;
}
.header {
    margin-bottom: 22px;
    padding-bottom: 14px;
    border-bottom: 1px solid #DCE3E2;
}
.header .logo {
    font-size: 12pt;
    font-weight: bold;
    color: #1E3135;
}
.ticket-title,
.doc-title {
    font-size: 16pt;
    font-weight: bold;
    color: #2B4347;
    margin-top: 14px;
    line-height: 1.25;
}
.ticket-subtitle,
.ticket-number,
.doc-subtitle {
    font-size: 10pt;
    color: #55676A;
    margin-top: 2px;
}

/* Abschnitte und Informationsraster */
.section {
    margin-bottom: 20px;
}
.section-title {
    font-size: 10pt;
    font-weight: bold;
    color: #2B4347;
    margin-bottom: 8px;
    padding-bottom: 4px;
    border-bottom: 1px solid #DCE3E2;
}
.info-grid {
    display: table;
    width: 100%;
}
.info-row {
    display: table-row;
}
.info-label {
    display: table-cell;
    color: #55676A;
    padding: 5px 15px 5px 0;
    width: 32%;
    vertical-align: top;
}
.info-value {
    display: table-cell;
    padding: 5px 0;
    color: #1E3135;
    vertical-align: top;
}
.info-value.code {
    font-family: 'DejaVu Sans Mono', monospace;
}
.info-value.link {
    word-break: break-all;
    color: #2B4347;
}

/* Zonen, QR-Code, Referenznummern */
.qr-section,
.attendee-highlight,
.zone {
    margin: 18px 0;
    padding: 14px 16px;
    background: #F3F6F5;
    border: 1px solid #DCE3E2;
    border-radius: 8px;
}
.qr-section {
    text-align: center;
}
.qr-code {
    margin: 10px auto;
}
.qr-code img {
    width: 180px;
    height: 180px;
}
.qr-hint {
    font-size: 8pt;
    color: #55676A;
    margin: 4px 0 6px;
}
.booking-reference,
.ticket-reference {
    font-family: 'DejaVu Sans Mono', monospace;
    font-size: 14pt;
    font-weight: bold;
    color: #1E3135;
    margin: 8px 0;
    letter-spacing: 1px;
}
.attendee-highlight {
    text-align: left;
}
.attendee-name {
    font-size: 15pt;
    font-weight: bold;
    color: #1E3135;
    margin: 2px 0 4px;
}

/* Abzeichen: Kontur, Fläche und Text */
.status-badge,
.ticket-type-badge {
    display: inline-block;
    padding: 2px 10px;
    border: 1px solid #B9D98A;
    background: #EEF6E0;
    color: #2C5E1A;
    border-radius: 10px;
    font-size: 8pt;
    font-weight: bold;
}
.ticket-type-badge {
    border-color: #DCE3E2;
    background: #FFFFFF;
    color: #2B4347;
}
.status-warning {
    border-color: #E2C88A;
    background: #FBF3DF;
    color: #6B4A0A;
}
.status-cancelled,
.status-error {
    border-color: #E3A9A9;
    background: #FCEEEE;
    color: #9B1C1C;
}

/* Meldungen: Fläche, Kontur, Text */
.note,
.important-info {
    margin: 16px 0;
    padding: 10px 14px;
    background: #F3F6F5;
    border: 1px solid #DCE3E2;
    border-radius: 6px;
    color: #1E3135;
    font-size: 8.5pt;
    line-height: 1.5;
}
.note h4,
.important-info .note-title {
    font-size: 9pt;
    margin-bottom: 4px;
    color: #1E3135;
}
.important-info p {
    margin: 3px 0;
}
.note-success {
    background: #EEF6E0;
    border-color: #B9D98A;
    color: #2C5E1A;
}
.note-success h4 {
    color: #2C5E1A;
}
.note-warning {
    background: #FBF3DF;
    border-color: #E2C88A;
    color: #6B4A0A;
}
.note-warning h4 {
    color: #6B4A0A;
}
.note-error {
    background: #FCEEEE;
    border-color: #E3A9A9;
    color: #9B1C1C;
}
.note-error h4 {
    color: #9B1C1C;
}

/* Tabellen: Linien statt gefüllter Köpfe, Beträge rechtsbündig */
table.data,
.tickets-table,
.items-table,
table.items {
    width: 100%;
    border-collapse: collapse;
    margin: 8px 0 12px;
}
table.data th,
.tickets-table th,
.items-table th,
table.items th {
    padding: 6px 8px;
    text-align: left;
    font-size: 8pt;
    font-weight: bold;
    color: #55676A;
    border-bottom: 1px solid #1E3135;
}
table.data td,
.tickets-table td,
.items-table td,
table.items td {
    padding: 6px 8px;
    border-bottom: 1px solid #DCE3E2;
    vertical-align: top;
}
table.data th.num,
.tickets-table th.num,
.items-table th.num,
table.items th.num,
.text-right {
    text-align: right;
}
.text-center {
    text-align: center;
}
.total-row td {
    font-weight: bold;
    color: #1E3135;
}
.tickets-table .total-row td {
    border-top: 1px solid #1E3135;
}

/* Summenblock (Rechnungen) */
.totals {
    width: 52%;
    margin: 6px 0 16px 48%;
}
.totals table {
    width: 100%;
    border-collapse: collapse;
}
.totals td {
    padding: 4px 8px;
    border-bottom: 1px solid #DCE3E2;
    color: #1E3135;
}
.totals td:first-child {
    color: #55676A;
}
.totals td:last-child {
    text-align: right;
}
.totals .total-row td {
    border-top: 1px solid #1E3135;
    border-bottom: 1px solid #1E3135;
    padding: 7px 8px;
    font-size: 10.5pt;
    font-weight: bold;
    color: #1E3135;
}
.totals .sub td {
    font-size: 7.5pt;
    color: #55676A;
}

/* Fuß */
.footer {
    margin-top: 26px;
    padding-top: 10px;
    border-top: 1px solid #DCE3E2;
    text-align: center;
    font-size: 7.5pt;
    color: #55676A;
}
.footer p {
    margin-bottom: 2px;
}
