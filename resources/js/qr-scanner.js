// QR-Scanner für den Check-in. Lokal gebündelt statt von unpkg geladen
// (die Content-Security-Policy erlaubt keine fremden Skript-Quellen).
import { Html5Qrcode } from 'html5-qrcode';

window.Html5Qrcode = Html5Qrcode;
