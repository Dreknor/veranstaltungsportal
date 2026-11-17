<x-layouts.app title="Abzeichen bearbeiten">
    <x-ui.page-header :title="'Abzeichen bearbeiten: ' . $badge->name" :back-href="route('admin.badges.index')" back-label="Zur Übersicht der Abzeichen" />

    @php
        $requirements = $badge->requirements ?? [];
        $requirements = is_array($requirements) ? $requirements : [];
        $currentRequirementType = old(
            'requirement_type',
            count($requirements) > 0 ? array_key_first($requirements) : '',
        );
        $currentRequirementValue = old('requirement_value', count($requirements) > 0 ? reset($requirements) : 1);
    @endphp

    <form method="POST" action="{{ route('admin.badges.update', $badge) }}" enctype="multipart/form-data"
        class="max-w-form">
        @csrf
        @method('PUT')

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grunddaten" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Name des Abzeichens" name="name" :value="$badge->name" required />
                <x-ui.textarea label="Beschreibung" name="description" :value="$badge->description" rows="3" required />
                <x-ui.select label="Typ" name="type" placeholder="Typ auswählen..." required :selected="$badge->type"
                    :options="['attendance' => 'Teilnahme', 'achievement' => 'Erfolge', 'special' => 'Spezial']" />
            </div>
        </x-ui.section>

        <x-ui.section title="Darstellung" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                @include('admin.badges._icon-field', ['current' => $badge->icon])

                <div>
                    <x-ui.label for="image" optional>Bild des Abzeichens</x-ui.label>

                    @if ($badge->image_path)
                        <!-- Current Image -->
                        <div class="mb-4 flex flex-wrap items-center gap-4">
                            <img src="{{ asset($badge->image_path) }}" alt="{{ $badge->name }}"
                                class="h-24 w-24 rounded-full border-4 border-line object-cover" id="currentImage">
                            <div>
                                <p class="m-0 text-small text-muted">Aktuelles Bild</p>
                                <x-ui.checkbox name="remove_image" id="removeImageCheckbox" value="1"
                                    label="Bild entfernen" :old="false" />
                            </div>
                        </div>
                    @endif

                    <x-ui.file-input name="image" id="image" accept="image/*"
                        aria-describedby="image-hint image-error" />
                    <x-ui.hint id="image-hint">
                        @if ($badge->image_path)
                            Neues Bild hochladen, um das aktuelle zu ersetzen.
                        @else
                            PNG oder SVG, 200 × 200 px oder größer, höchstens 2 MB.
                        @endif
                    </x-ui.hint>
                    <x-ui.field-error name="image" id="image-error" />

                    <!-- New Image Preview -->
                    <div id="imagePreview" class="mt-3 hidden">
                        <p class="m-0 mb-2 text-small font-medium text-ink">Neues Bild (Vorschau):</p>
                        <img src="" alt="Vorschau"
                            class="h-32 w-32 rounded-full border-4 border-ink object-cover">
                    </div>
                </div>

                <div>
                    <x-ui.label for="color" required>Farbe (Hex-Code)</x-ui.label>
                    <div class="flex flex-wrap items-center gap-3">
                        <input type="color" name="color" id="color" value="{{ old('color', $badge->color) }}"
                            class="h-12 w-20 cursor-pointer rounded-control border border-line-strong bg-surface p-1">
                        <x-ui.input id="colorText" :value="(old('color', $badge->color))" placeholder="#2B4347"
                            aria-label="Hex-Code der Farbe" :old="false" class="min-w-0 max-w-48 flex-1" />
                        <span id="colorPreview"
                            class="inline-flex h-12 w-12 flex-none items-center justify-center rounded-full"
                            style="background-color: {{ old('color', $badge->color) }}20; color: {{ old('color', $badge->color) }}"
                            title="Vorschau des Abzeichens">
                            <x-icon.medal class="h-6 w-6" />
                        </span>
                    </div>
                    <x-ui.field-error name="color" />
                </div>
            </div>
        </x-ui.section>

        <x-ui.section title="Vergabe" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Punkte" name="points" type="number" :value="$badge->points" min="0" max="1000"
                    required />
                <x-ui.select label="Anforderungstyp" name="requirement_type" placeholder="Typ auswählen..." required
                    :old="false" :options="\App\Models\Badge::REQUIREMENT_TYPES" :selected="\App\Models\Badge::normalizeRequirement((string) $currentRequirementType)" />
                <x-ui.field label="Anforderungswert" name="requirement_value" type="number" :old="false"
                    :value="$currentRequirementValue" min="1" required
                    hint="Anzahl, die erreicht werden muss, um das Abzeichen zu erhalten." />
            </div>
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Änderungen speichern</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.badges.index')">Abbrechen</x-ui.button>
            @if ($badge->users()->count() === 0)
                <x-ui.button variant="danger" type="submit" form="delete-badge-form" class="sm:ml-auto">Abzeichen
                    löschen</x-ui.button>
            @endif
        </div>
    </form>

    @if ($badge->users()->count() === 0)
        <form id="delete-badge-form" method="POST" action="{{ route('admin.badges.destroy', $badge) }}" class="hidden"
            onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Abzeichen löschen möchten?')">
            @csrf
            @method('DELETE')
        </form>
    @endif

    @push('scripts')
        <script>
            // Color picker synchronization
            const colorPicker = document.getElementById('color');
            const colorText = document.getElementById('colorText');
            const colorPreview = document.getElementById('colorPreview');

            function updateColorPreview(value) {
                colorPreview.style.backgroundColor = value + '20';
                colorPreview.style.color = value;
            }

            colorPicker.addEventListener('input', function() {
                colorText.value = this.value;
                updateColorPreview(this.value);
            });

            colorText.addEventListener('input', function() {
                if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                    colorPicker.value = this.value;
                    updateColorPreview(this.value);
                }
            });

            // Image preview
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = imagePreview.querySelector('img');
            const removeCheckbox = document.getElementById('removeImageCheckbox');
            const currentImage = document.getElementById('currentImage');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        if (removeCheckbox) {
                            removeCheckbox.checked = false;
                        }
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                }
            });

            // Toggle current image visibility when remove is checked
            if (removeCheckbox && currentImage) {
                removeCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        currentImage.style.opacity = '0.3';
                        currentImage.style.filter = 'grayscale(100%)';
                    } else {
                        currentImage.style.opacity = '1';
                        currentImage.style.filter = 'none';
                    }
                });
            }
        </script>
    @endpush
</x-layouts.app>
