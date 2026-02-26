import "./bootstrap";
import Alpine from "alpinejs";
import * as CookieConsent from "vanilla-cookieconsent";
import { richEditor } from "./rich-editor";

window.Alpine = Alpine;
window.CookieConsent = CookieConsent;
Alpine.start();

/**
 * Cookie-Consent Konfiguration (DSGVO-konform)
 *
 * Kategorien:
 * - necessary: Immer aktiv (Session, CSRF)
 * - functional: reCAPTCHA, PayPal (opt-in)
 */
CookieConsent.run({
    // Einwilligung bei erneutem Besuch nach 6 Monaten erneut einholen
    revision: 1,

    guiOptions: {
        consentModal: {
            layout: "box",
            position: "bottom left",
        },
        preferencesModal: {
            layout: "box",
        },
    },

    onConsent: ({ cookie }) => {
        // reCAPTCHA nachladen wenn functional akzeptiert wurde
        if (CookieConsent.acceptedCategory("functional")) {
            loadRecaptcha();
        } else {
            // Funktionale Cookies abgelehnt – Warnhinweis anzeigen
            showRecaptchaWarnings(true);
        }
    },

    onChange: ({ changedCategories }) => {
        if (changedCategories.includes("functional")) {
            if (CookieConsent.acceptedCategory("functional")) {
                loadRecaptcha();
                showRecaptchaWarnings(false);
            } else {
                // Seite neu laden um Scripts zu entfernen
                window.location.reload();
            }
        }
    },

    categories: {
        necessary: {
            enabled: true,
            readOnly: true,
        },
        functional: {
            enabled: false,
            readOnly: false,
        },
    },

    language: {
        default: "de",
        translations: {
            de: {
                consentModal: {
                    title: "Wir verwenden Cookies",
                    description:
                        "Diese Website verwendet technisch notwendige Cookies für den sicheren Betrieb. Mit Ihrer Einwilligung aktivieren wir auch Dienste wie reCAPTCHA (Google) und PayPal für den Buchungsvorgang. " +
                        'Weitere Informationen finden Sie in unserer <a href="/datenschutz" class="cc__link">Datenschutzerklärung</a>.',
                    acceptAllBtn: "Alle akzeptieren",
                    acceptNecessaryBtn: "Nur notwendige",
                    showPreferencesBtn: "Einstellungen",
                },
                preferencesModal: {
                    title: "Cookie-Einstellungen",
                    acceptAllBtn: "Alle akzeptieren",
                    acceptNecessaryBtn: "Nur notwendige",
                    savePreferencesBtn: "Einstellungen speichern",
                    closeIconLabel: "Schließen",
                    sections: [
                        {
                            title: "Notwendige Cookies",
                            description:
                                "Diese Cookies sind für den sicheren Betrieb der Website erforderlich (Session-Verwaltung, CSRF-Schutz). Sie können nicht deaktiviert werden.",
                            linkedCategory: "necessary",
                        },
                        {
                            title: "Funktionale Cookies",
                            description:
                                "Diese Dienste werden für den Buchungsvorgang benötigt: Google reCAPTCHA (Spam-Schutz) und PayPal (Zahlungsabwicklung). Beide Dienste übermitteln Daten an US-Anbieter.",
                            linkedCategory: "functional",
                            cookieTable: {
                                headers: {
                                    name: "Name",
                                    domain: "Anbieter",
                                    desc: "Zweck",
                                },
                                body: [
                                    {
                                        name: "_GRECAPTCHA",
                                        domain: "google.com",
                                        desc: "Spam-Schutz durch reCAPTCHA v3",
                                    },
                                    {
                                        name: "paypal_*",
                                        domain: "paypal.com",
                                        desc: "Zahlungsabwicklung über PayPal",
                                    },
                                ],
                            },
                        },
                    ],
                },
            },
        },
    },
});

/**
 * Warnhinweise ein-/ausblenden wenn funktionale Cookies nicht erlaubt sind
 */
function showRecaptchaWarnings(show) {
    document.querySelectorAll('[id^="recaptcha-cookie-warning-"]').forEach(el => {
        if (show) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
}

/**
 * reCAPTCHA dynamisch laden (nur nach Einwilligung)
 */
function loadRecaptcha() {
    const siteKey = document.querySelector('meta[name="recaptcha-site-key"]')?.content;
    if (!siteKey) return;
    if (document.getElementById("recaptcha-script")) return; // bereits geladen

    const script = document.createElement("script");
    script.id = "recaptcha-script";
    script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);

    // Benachrichtige Formulare, dass reCAPTCHA jetzt verfügbar ist
    script.onload = () => {
        document.dispatchEvent(new CustomEvent("recaptcha:loaded"));
    };
}

// Beim Seitenload prüfen ob Consent bereits erteilt wurde
document.addEventListener("DOMContentLoaded", () => {
    if (CookieConsent.acceptedCategory("functional")) {
        loadRecaptcha();
    } else if (CookieConsent.validConsent()) {
        // Nutzer hat bereits entschieden, aber funktionale Cookies abgelehnt
        showRecaptchaWarnings(true);
    }
});

// Cookie-Einstellungen-Button in Footer verdrahten
window.showCookiePreferences = () => CookieConsent.showPreferences();


