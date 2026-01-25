<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Badge bearbeiten: {{ $badge->name }}
            </h2>
            <a href="{{ route('admin.badges.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>
                Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                <form method="POST" action="{{ route('admin.badges.update', $badge) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Badge Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $badge->name) }}" class="input" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Beschreibung <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="3" class="input" required>{{ old('description', $badge->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Badge Typ <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" class="input" required>
                            <option value="">Typ auswählen...</option>
                            <option value="attendance" {{ old('type', $badge->type) === 'attendance' ? 'selected' : '' }}>Teilnahme</option>
                            <option value="achievement" {{ old('type', $badge->type) === 'achievement' ? 'selected' : '' }}>Erfolge</option>
                            <option value="special" {{ old('type', $badge->type) === 'special' ? 'selected' : '' }}>Spezial</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Icon -->
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Icon (Font Awesome Klasse) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $badge->icon) }}" class="input" required placeholder="fas fa-medal">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Beispiele: fas fa-medal, fas fa-trophy, fas fa-star, fas fa-crown
                        </p>
                        @error('icon')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Badge Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Badge-Bild
                        </label>

                        @if($badge->image_path)
                            <!-- Current Image -->
                            <div class="mb-4 space-y-3">
                                <div class="flex items-center gap-4">
                                    <img src="{{ asset($badge->image_path) }}"
                                         alt="{{ $badge->name }}"
                                         class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 dark:border-gray-700"
                                         id="currentImage">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Aktuelles Bild</p>
                                        <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                            <input type="checkbox" name="remove_image" value="1" class="rounded" id="removeImageCheckbox">
                                            <span class="text-sm text-red-600 dark:text-red-400">Bild entfernen</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="space-y-3">
                            <input type="file"
                                   name="image"
                                   id="image"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-lg file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-blue-50 file:text-blue-700
                                          hover:file:bg-blue-100
                                          dark:file:bg-blue-900 dark:file:text-blue-300
                                          dark:hover:file:bg-blue-800
                                          cursor-pointer">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                @if($badge->image_path)
                                    Neues Bild hochladen um das aktuelle zu ersetzen
                                @else
                                    PNG oder SVG, 200x200px oder größer. Max. 2MB
                                @endif
                            </p>

                            <!-- New Image Preview -->
                            <div id="imagePreview" class="hidden mt-3">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Neues Bild (Vorschau):</p>
                                <img src="" alt="Vorschau" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                            </div>
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Farbe (Hex-Code) <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="color" name="color" id="color" value="{{ old('color', $badge->color) }}" class="h-10 w-20 rounded border border-gray-300 dark:border-gray-600">
                            <input type="text" id="colorText" value="{{ old('color', $badge->color) }}" class="input" placeholder="#3B82F6">
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Points -->
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Punkte <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="points" id="points" value="{{ old('points', $badge->points) }}" min="0" max="1000" class="input" required>
                        @error('points')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requirement Type -->
                    <div>
                        <label for="requirement_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Anforderungstyp <span class="text-red-500">*</span>
                        </label>
                        @php
                            $currentRequirementType = old('requirement_type') ?: (is_array($badge->requirements) && count($badge->requirements) > 0 ? array_key_first($badge->requirements) : '');
                        @endphp
                        <select name="requirement_type" id="requirement_type" class="input" required>
                            <option value="">Typ auswählen...</option>
                            <option value="events_attended" {{ $currentRequirementType === 'events_attended' ? 'selected' : '' }}>Events besucht</option>
                            <option value="hours_attended" {{ $currentRequirementType === 'hours_attended' ? 'selected' : '' }}>Stunden teilgenommen (Einzelevent)</option>
                            <option value="total_hours_attended" {{ $currentRequirementType === 'total_hours_attended' ? 'selected' : '' }}>Gesamt-Stunden teilgenommen</option>
                            <option value="bookings_made" {{ $currentRequirementType === 'bookings_made' ? 'selected' : '' }}>Buchungen getätigt</option>
                            <option value="reviews_written" {{ $currentRequirementType === 'reviews_written' ? 'selected' : '' }}>Bewertungen geschrieben</option>
                            <option value="connections_made" {{ $currentRequirementType === 'connections_made' ? 'selected' : '' }}>Verbindungen hergestellt</option>
                            <option value="events_organized" {{ $currentRequirementType === 'events_organized' ? 'selected' : '' }}>Events organisiert</option>
                            <option value="categories_explored" {{ $currentRequirementType === 'categories_explored' ? 'selected' : '' }}>Verschiedene Kategorien besucht</option>
                            <option value="early_bird_bookings" {{ $currentRequirementType === 'early_bird_bookings' ? 'selected' : '' }}>Frühbucher-Buchungen (7+ Tage vorher)</option>
                            <option value="revenue_generated" {{ $currentRequirementType === 'revenue_generated' ? 'selected' : '' }}>Umsatz generiert (€)</option>
                            <option value="participants_reached" {{ $currentRequirementType === 'participants_reached' ? 'selected' : '' }}>Teilnehmer erreicht</option>
                        </select>
                        @error('requirement_type')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requirement Value -->
                    <div>
                        <label for="requirement_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Anforderungswert <span class="text-red-500">*</span>
                        </label>
                        @php
                            $currentRequirementValue = old('requirement_value') ?: (is_array($badge->requirements) && count($badge->requirements) > 0 ? reset($badge->requirements) : 1);
                        @endphp
                        <input type="number" name="requirement_value" id="requirement_value" value="{{ $currentRequirementValue }}" min="1" class="input" required>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Anzahl, die erreicht werden muss, um den Badge zu erhalten
                        </p>
                        @error('requirement_value')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-4">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>
                            Änderungen speichern
                        </button>
                        <a href="{{ route('admin.badges.index') }}" class="btn-secondary">
                            Abbrechen
                        </a>
                        @if($badge->users()->count() === 0)
                            <form method="POST" action="{{ route('admin.badges.destroy', $badge) }}" class="ml-auto" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Badge löschen möchten?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">
                                    <i class="fas fa-trash mr-2"></i>
                                    Badge löschen
                                </button>
                            </form>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Color picker synchronization
        const colorPicker = document.getElementById('color');
        const colorText = document.getElementById('colorText');

        colorPicker.addEventListener('input', function() {
            colorText.value = this.value;
        });

        colorText.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                colorPicker.value = this.value;
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

