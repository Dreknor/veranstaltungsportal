<x-layouts.app title="Neue Organisation erstellen">
    <x-ui.page-header title="Neue Organisation erstellen"
        subtitle="Legen Sie eine neue Organisation an, um Veranstaltungen zu verwalten."
        :back-href="route('organizer.organizations.select')" back-label="Zur Auswahl" />

    <form method="POST" action="{{ route('organizer.organizations.store') }}" enctype="multipart/form-data"
        class="max-w-form">
        @csrf

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grundinformationen" level="h2" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Name" name="name" required placeholder="Name Ihrer Organisation" />
                <x-ui.textarea label="Beschreibung" name="description" optional rows="4"
                    placeholder="Kurze Beschreibung Ihrer Organisation" />
            </div>
        </x-ui.section>

        <x-ui.section title="Kontaktinformationen" level="h2" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Website" name="website" type="url" optional placeholder="https://..." />
                    <x-ui.field label="E-Mail" name="email" type="email" optional placeholder="info@organisation.de" />
                    <x-ui.field label="Telefon" name="phone" optional placeholder="+49 123 456789" />
                </div>
                <div>
                    <x-ui.label for="logo" optional>Logo</x-ui.label>
                    <x-ui.input type="file" name="logo" id="logo" accept="image/*" aria-describedby="logo-hint" />
                    <x-ui.hint id="logo-hint">Empfohlen: 200 × 200 px, höchstens 2 MB</x-ui.hint>
                    <x-ui.field-error name="logo" />
                </div>
            </div>
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Organisation erstellen</x-ui.button>
            <x-ui.button variant="secondary" :href="route('organizer.organizations.select')">Abbrechen</x-ui.button>
        </div>
    </form>
</x-layouts.app>
