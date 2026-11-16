<x-layouts.app title="Neues Abzeichen anlegen">
    <x-ui.page-header title="Neues Abzeichen anlegen" :back-href="route('admin.badges.index')" back-label="Zur Übersicht der Abzeichen" />

    <form method="POST" action="{{ route('admin.badges.store') }}" enctype="multipart/form-data" class="max-w-form">
        @csrf

        {{-- Standardwert der gespeicherten Icon-Bezeichnung (unverändert gegenüber dem bisherigen Wert) --}}
        @php $defaultIcon = 'fas' . ' fa-medal'; @endphp

        <p class="m-0 mb-6 text-small text-muted">* Pflichtfeld</p>

        <x-ui.section title="Grunddaten" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Name des Abzeichens" name="name" required />
                <x-ui.textarea label="Beschreibung" name="description" rows="3" required />
                <x-ui.select label="Typ" name="type" placeholder="Typ auswählen..." required
                    :options="['attendance' => 'Teilnahme', 'achievement' => 'Erfolge', 'special' => 'Spezial']" />
            </div>
        </x-ui.section>

        <x-ui.section title="Darstellung" class="border-t border-line py-8">
            <div class="flex flex-col gap-y-5">
                <x-ui.field label="Icon" name="icon" :value="$defaultIcon" :placeholder="$defaultIcon" required
                    hint="Name eines Symbols, z. B. medal, star, academic-cap oder ein Font-Awesome-Name wie fas fa-trophy. Ohne eigenes Bild zeigt das Abzeichen dieses Symbol; unbekannte Namen zeigen die Medaille." />

                <div>
                    <x-ui.label for="image" optional>Bild des Abzeichens</x-ui.label>
                    <x-ui.input type="file" name="image" id="image" accept="image/*" aria-describedby="image-hint image-error" />
                    <x-ui.hint id="image-hint">Empfohlen: PNG oder SVG, 200 × 200 px oder größer, höchstens 2 MB.</x-ui.hint>
                    <x-ui.field-error name="image" id="image-error" />

                    <!-- Image Preview -->
                    <div id="imagePreview" class="mt-3 hidden">
                        <img src="" alt="Vorschau"
                            class="h-32 w-32 rounded-full border-4 border-line object-cover">
                    </div>
                </div>

                <div>
                    <x-ui.label for="color" required>Farbe (Hex-Code)</x-ui.label>
                    <div class="flex flex-wrap items-center gap-3">
                        <input type="color" name="color" id="color" value="{{ old('color', '#3B82F6') }}"
                            class="h-12 w-20 cursor-pointer rounded-control border border-line-strong bg-surface p-1">
                        <x-ui.input id="colorText" value="{{ old('color', '#3B82F6') }}" placeholder="#3B82F6"
                            aria-label="Hex-Code der Farbe" :old="false" class="min-w-0 max-w-48 flex-1" />
                        <span id="colorPreview"
                            class="inline-flex h-12 w-12 flex-none items-center justify-center rounded-full"
                            style="background-color: {{ old('color', '#3B82F6') }}20; color: {{ old('color', '#3B82F6') }}"
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
                <x-ui.field label="Punkte" name="points" type="number" value="10" min="0" max="1000" required />
                <x-ui.select label="Anforderungstyp" name="requirement_type" placeholder="Typ auswählen..." required
                    :old="false" :options="\App\Models\Badge::REQUIREMENT_TYPES"
                    :selected="\App\Models\Badge::normalizeRequirement((string) old('requirement_type'))" />
                <x-ui.field label="Anforderungswert" name="requirement_value" type="number" value="1" min="1" required
                    hint="Anzahl, die erreicht werden muss, um das Abzeichen zu erhalten." />
            </div>
        </x-ui.section>

        <div class="flex flex-wrap gap-3 border-t border-line pt-8">
            <x-ui.button type="submit">Abzeichen anlegen</x-ui.button>
            <x-ui.button variant="secondary" :href="route('admin.badges.index')">Abbrechen</x-ui.button>
        </div>
    </form>

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

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.classList.add('hidden');
                }
            });
        </script>
    @endpush
</x-layouts.app>
