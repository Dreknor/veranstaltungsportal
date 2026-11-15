<x-layouts.app title="Organizer-Profil bearbeiten">
    <x-ui.page-header title="Organizer-Profil bearbeiten" subtitle="Verwalten Sie Ihre persönlichen Profil-Informationen." />

    <div class="max-w-form">
        <x-ui.alert tone="info" class="mb-8">
            <strong>Hinweis:</strong> Organisationsdaten, Rechnungsinformationen und Bankverbindung verwalten Sie in den
            <a href="{{ route('organizer.organization.edit') }}">Organisations-Einstellungen</a>.
        </x-ui.alert>

        <form method="POST" action="{{ route('organizer.profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

            <x-ui.section title="Profilbild" description="Laden Sie ein Profilbild hoch (höchstens 2 MB)."
                class="border-t border-line py-8">
                <div class="flex flex-wrap items-center gap-6">
                    <img class="h-24 w-24 shrink-0 rounded-full border border-line object-cover"
                        src="{{ auth()->user()->profilePhotoUrl() }}" alt="Profilbild">
                    <div class="min-w-0 flex-1 basis-60">
                        <x-ui.label for="profile_photo">Profilbild hochladen</x-ui.label>
                        <x-ui.input type="file" name="profile_photo" id="profile_photo" accept="image/*" />
                        <x-ui.field-error name="profile_photo" />

                        @if (auth()->user()->profile_photo)
                            <x-ui.button type="submit" variant="link" form="delete-photo-form" class="mt-2">Profilbild löschen</x-ui.button>
                        @endif
                    </div>
                </div>
            </x-ui.section>

            <x-ui.section title="Persönliche Informationen" description="Ihre persönlichen Kontaktdaten."
                class="border-t border-line py-8">
                <div class="flex flex-col gap-y-5">
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                        <x-ui.field label="Vorname" name="first_name" :value="$user->first_name" required />
                        <x-ui.field label="Nachname" name="last_name" :value="$user->last_name" required />
                        <x-ui.field label="E-Mail" name="email" type="email" :value="$user->email" required />
                        <x-ui.field label="Telefon" name="phone" type="tel" :value="$user->phone" optional />
                    </div>
                    <x-ui.textarea label="Über mich" name="bio" :value="$user->bio" optional rows="4" maxlength="1000"
                        hint="Maximal 1000 Zeichen" />
                </div>
            </x-ui.section>

            <div class="border-t border-line pt-8">
                <x-ui.button type="submit">Profil aktualisieren</x-ui.button>
            </div>
        </form>

        @if (auth()->user()->profile_photo)
            <form id="delete-photo-form" method="POST" action="{{ route('organizer.profile.delete-photo') }}" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>
</x-layouts.app>
