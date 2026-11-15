<x-layouts.app title="Datenschutzeinstellungen">
    <x-ui.page-header title="Datenschutzeinstellungen" :back-href="route('data-privacy.index')" back-label="Zu Datenschutz & DSGVO" />

    <div class="max-w-form">
        <form method="POST" action="{{ route('data-privacy.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-2">
                <x-ui.checkbox name="allow_networking" label="Vernetzung erlauben"
                    hint="Andere Benutzer können mit Ihnen in Kontakt treten und Sie zu ihrem Netzwerk hinzufügen."
                    :checked="auth()->user()->allow_networking ?? true" />
                <x-ui.checkbox name="show_profile_public" label="Öffentliches Profil"
                    hint="Ihr Profil ist für alle Benutzer sichtbar, auch wenn sie nicht mit Ihnen verbunden sind."
                    :checked="auth()->user()->show_profile_public ?? false" />
                <x-ui.checkbox name="allow_data_analytics" label="Datenanalyse erlauben"
                    hint="Ihre anonymisierten Nutzungsdaten helfen uns, die Plattform zu verbessern."
                    :checked="auth()->user()->allow_data_analytics ?? true" />
            </div>

            <div class="mt-6">
                <x-ui.button type="submit">Einstellungen speichern</x-ui.button>
            </div>
        </form>

        <section aria-labelledby="rechte" class="mt-8 border-t border-line pt-8">
            <h2 id="rechte" class="m-0 mb-4 text-h3">Ihre Rechte nach DSGVO</h2>
            <ul class="m-0 flex list-disc flex-col gap-2 pl-5 text-base text-ink">
                <li><strong class="font-semibold">Recht auf Auskunft (Art. 15):</strong> Sie können eine Kopie Ihrer Daten anfordern.</li>
                <li><strong class="font-semibold">Recht auf Berichtigung (Art. 16):</strong> Sie können fehlerhafte Daten korrigieren lassen.</li>
                <li><strong class="font-semibold">Recht auf Löschung (Art. 17):</strong> Sie können die Löschung Ihrer Daten beantragen.</li>
                <li><strong class="font-semibold">Recht auf Datenübertragbarkeit (Art. 20):</strong> Sie können Ihre Daten in einem strukturierten Format erhalten.</li>
                <li><strong class="font-semibold">Recht auf Widerspruch (Art. 21):</strong> Sie können der Verarbeitung Ihrer Daten widersprechen.</li>
            </ul>
        </section>
    </div>
</x-layouts.app>
