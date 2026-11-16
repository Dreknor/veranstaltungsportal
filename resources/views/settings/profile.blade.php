<x-layouts.app title="Profil">
    <x-ui.page-header title="Einstellungen" class="mb-4" />

    @include('settings.partials.navigation')

    <div class="max-w-form">
        <x-ui.section :title="__('Profile')" :description="__('Update your name and email address')">
            <form action="{{ route('settings.profile.update') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col gap-y-5">
                @csrf
                @method('PUT')

                <!-- Profile Photo Section -->
                <div>
                    <x-ui.label for="profile_photo">{{ __('Profile Photo') }}</x-ui.label>
                    <div class="flex flex-wrap items-center gap-6">
                        <img class="h-24 w-24 shrink-0 rounded-full border border-line object-cover"
                            src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->fullName() }}">
                        <div class="min-w-0 flex-1 basis-60">
                            <x-ui.file-input name="profile_photo" button="Foto auswählen" accept="image/*"
                                aria-describedby="profile_photo-hint" />
                            <x-ui.hint id="profile_photo-hint">JPG, PNG oder GIF, höchstens 2 MB</x-ui.hint>
                            @if ($user->profile_photo)
                                <x-ui.button variant="link"
                                    onclick="if(confirm('{{ __('Are you sure you want to delete your profile photo?') }}')) { document.getElementById('delete-photo-form').submit(); }">
                                    {{ __('Remove photo') }}
                                </x-ui.button>
                            @endif
                        </div>
                    </div>
                    <x-ui.field-error name="profile_photo" />
                </div>

                <div class="grid grid-cols-[repeat(auto-fit,minmax(248px,1fr))] gap-x-4 gap-y-5">
                    <x-ui.field label="Vorname" name="first_name" :value="$user->first_name" />
                    <x-ui.field label="Nachname" name="last_name" :value="$user->last_name" />
                </div>

                <x-ui.field label="Anzeigename" name="name" :value="$user->name"
                    :hint="__('This is how other users will see you.')" />

                <x-ui.field label="E-Mail-Adresse" name="email" type="email" :value="$user->email" />

                <!-- Account Type Display (read-only) -->
                <div>
                    <x-ui.label as="span">{{ __('Account Type') }}</x-ui.label>
                    <p class="m-0 text-base font-medium text-ink">
                        {{ $user->isAdmin() ? 'Administrator' : ($user->isOrganizer() ? 'Veranstalter' : 'Teilnehmer') }}
                    </p>
                    <x-ui.hint>{{ __('Contact support to change your account type') }}</x-ui.hint>
                </div>

                <x-ui.field label="Telefon" optional name="phone" type="tel" :value="$user->phone" />

                <x-ui.textarea label="{{ __('Bio') }}" name="bio" :value="$user->bio" rows="4" maxlength="1000"
                    :hint="__('Brief description for your profile. Maximum 1000 characters.')" />

                <div>
                    <x-ui.button type="submit">{{ __('Save') }}</x-ui.button>
                </div>
            </form>

            <!-- Hidden form for deleting photo -->
            @if ($user->profile_photo)
                <form id="delete-photo-form" action="{{ route('settings.profile.photo.delete') }}" method="POST"
                    class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </x-ui.section>

        <!-- Delete Account Section -->
        <x-ui.section :title="__('Delete account')" :description="__('Delete your account and all of its resources')"
            level="h2" class="mt-12 border-t border-line pt-8">
            <form action="{{ route('settings.profile.destroy') }}" method="POST"
                onsubmit="return confirm('{{ __('Are you sure you want to delete your account?') }}')">
                @csrf
                @method('DELETE')
                <x-ui.button variant="danger" type="submit">{{ __('Delete account') }}</x-ui.button>
            </form>
        </x-ui.section>
    </div>
</x-layouts.app>
