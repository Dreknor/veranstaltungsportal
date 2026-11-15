<x-layouts.app title="Organisation bearbeiten">
    <x-ui.page-header title="Organisation bearbeiten" subtitle="Verwalten Sie die Einstellungen Ihrer Organisation.">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('organizer.team.index')">
                <x-slot:icon><x-icon.users /></x-slot:icon>
                Team verwalten
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="max-w-form">
        <x-ui.alert tone="info" title="PayPal als Zahlungsmethode aktivieren">
            Sie können Ihren Kunden ermöglichen, direkt mit PayPal zu bezahlen.
            <a href="{{ route('organizer.organization.paypal') }}">Richten Sie jetzt Ihre PayPal-Einstellungen ein.</a>
            @if ($organization->hasPayPalConfigured())
                <span class="mt-2 block">
                    <x-ui.status tone="success">PayPal ist konfiguriert ({{ ucfirst($organization->paypal_mode) }})</x-ui.status>
                </span>
            @endif
        </x-ui.alert>

        <form method="POST" action="{{ route('organizer.organization.update') }}" enctype="multipart/form-data"
            class="mt-8">
            @csrf
            @method('PUT')

            <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

            <x-ui.section title="Logo" level="h2" class="border-t border-line py-8">
                <div class="flex flex-wrap items-start gap-6">
                    <div id="logoPreview"
                        class="h-32 w-32 shrink-0 overflow-hidden rounded-control border border-line bg-zone">
                        @if ($organization->logo)
                            <img src="{{ asset('storage/' . $organization->logo) }}" class="h-full w-full object-cover"
                                alt="Logo">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-h2 text-ink">
                                {{ $organization->initials() }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1 basis-60">
                        <x-ui.label for="logoInput">Logo hochladen</x-ui.label>
                        <x-ui.input type="file" name="logo" id="logoInput" accept="image/*" onchange="previewLogo(event)"
                            aria-describedby="logoInput-hint" />
                        <x-ui.hint id="logoInput-hint">Empfohlen: quadratisches Bild, mindestens 200 × 200 px (PNG oder JPG, höchstens 2 MB)</x-ui.hint>
                        <x-ui.field-error name="logo" id="logoInput-error" />

                        @if ($organization->logo)
                            <div class="mt-3">
                                <x-ui.button variant="secondary" size="sm" onclick="deleteLogo()">Logo entfernen</x-ui.button>
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.section>

            <x-ui.section title="Grundinformationen" level="h2" class="border-t border-line py-8">
                <div class="flex flex-col gap-y-5">
                    <x-ui.field label="Name" name="name" :value="$organization->name" required />
                    <x-ui.textarea label="Beschreibung" name="description" :value="$organization->description" optional
                        rows="4" />
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Website" name="website" type="url" :value="$organization->website" optional
                            placeholder="https://..." />
                        <x-ui.field label="E-Mail" name="email" type="email" :value="$organization->email" optional />
                        <x-ui.field label="Telefon" name="phone" :value="$organization->phone" optional />
                    </div>
                </div>
            </x-ui.section>

            <div class="flex flex-wrap gap-3 border-t border-line pt-8">
                <x-ui.button type="submit">Änderungen speichern</x-ui.button>
                <x-ui.button variant="secondary" :href="route('organizer.organization.paypal')">PayPal-Einstellungen</x-ui.button>
                <x-ui.button variant="secondary" :href="route('organizer.bank-account.index')">Rechnungsdaten</x-ui.button>
                <x-ui.button variant="secondary" :href="route('organizer.organizations.select')">Zurück zur Auswahl</x-ui.button>
            </div>
        </form>
    </div>

    <!-- Delete Logo Form (hidden) -->
    <form id="deleteLogoForm" method="POST" action="{{ route('organizer.organization.delete-logo') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            function previewLogo(event) {
                const preview = document.getElementById('logoPreview');
                const file = event.target.files[0];
                if (file && file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = '<img src="' + e.target.result +
                            '" class="h-full w-full object-cover" alt="Logo Vorschau">';
                    }
                    reader.readAsDataURL(file);
                }
            }

            function deleteLogo() {
                if (confirm('Logo wirklich entfernen?')) {
                    document.getElementById('deleteLogoForm').submit();
                }
            }
        </script>
    @endpush
</x-layouts.app>
