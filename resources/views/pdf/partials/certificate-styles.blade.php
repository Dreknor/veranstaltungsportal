{{-- Zusätze nur für Zertifikate (A4 quer), Ergänzung zu pdf.partials.styles --}}
@page {
    margin: 0;
    size: A4 landscape;
}
body {
    font-size: 11pt;
}
.certificate-frame {
    margin: 28px;
    height: 660px;
    border: 2px solid #2B4347;
    padding: 6px;
}
.certificate-inner {
    height: 644px;
    border: 1px solid #DCE3E2;
}
.certificate-table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
}
.certificate-table > tbody > tr > td {
    padding: 0 40px;
}
.certificate-top {
    height: 70px;
    padding-top: 24px !important;
    text-align: center;
    vertical-align: top;
}
.certificate-top .brand {
    margin-bottom: 0;
}
.certificate-main {
    vertical-align: middle;
}
.certificate-title {
    font-size: 30pt;
    font-weight: bold;
    color: #2B4347;
}
.certificate-rule {
    width: 80px;
    margin: 12px auto 18px;
    border-top: 3px solid #8DC11D;
}
.awarded-to {
    font-size: 11pt;
    color: #55676A;
    margin-bottom: 6px;
}
.cert-name {
    display: inline-block;
    min-width: 380px;
    margin: 4px 0 12px;
    padding: 4px 20px 8px;
    font-size: 26pt;
    border-bottom: 1px solid #1E3135;
}
.completion-text {
    font-size: 11pt;
    color: #55676A;
    margin: 6px 0;
}
.event-title {
    font-size: 17pt;
    font-weight: bold;
    color: #2B4347;
    margin: 6px 0;
}
.event-info {
    font-size: 10.5pt;
    color: #55676A;
    margin: 6px 0;
}
.certificate-bottom {
    height: 110px;
    vertical-align: bottom;
    padding-bottom: 16px !important;
}
.signatures {
    width: 100%;
    border-collapse: collapse;
}
.signatures td {
    width: 40%;
    text-align: center;
    vertical-align: top;
}
.signatures td.signature-gap {
    width: 20%;
}
.signature-line {
    border-top: 1px solid #1E3135;
    margin: 28px 20px 4px;
}
.signature-label {
    font-size: 8.5pt;
    color: #55676A;
}
.signature-value {
    font-size: 10pt;
    margin-top: 2px;
}
.certificate-number {
    margin-top: 12px;
    text-align: right;
    font-size: 8pt;
    color: #55676A;
}
.certificate-refs {
    width: 100%;
    margin-top: 12px;
    border-collapse: collapse;
    font-size: 8pt;
    color: #55676A;
    text-align: left;
}
.certificate-refs td.num {
    text-align: right;
}
