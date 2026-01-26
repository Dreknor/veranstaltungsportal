<x-layouts.app title="Organisation bearbeiten">
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Organisation bearbeiten</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Verwalten Sie die Einstellungen Ihrer Organisation</p>
        </div>
        <a href="{{ route('organizer.team.index') }}"
           class="inline-flex items-center px-4 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Team verwalten
        </a>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 rounded">
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded">
            <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    <!-- PayPal Info Box -->
    <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l.506-3.219 1.088-6.894a1.344 1.344 0 0 1 1.33-1.139h2.19c3.51 0 6.146-1.432 7.071-5.788.029-.14.054-.28.076-.421.018-.113.029-.226.038-.339a9.189 9.189 0 0 1 1.924.879z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                    PayPal als Zahlungsmethode aktivieren
                </h3>
                <p class="mt-1 text-sm text-blue-800 dark:text-blue-200">
                    Sie können Ihren Kunden ermöglichen, direkt mit PayPal zu bezahlen.
                    <a href="{{ route('organizer.organization.paypal') }}" class="font-semibold underline hover:no-underline">
                        Richten Sie jetzt Ihre PayPal-Einstellungen ein →
                    </a>
                </p>
                @if($organization->hasPayPalConfigured())
                    <div class="mt-2 inline-flex items-center text-xs text-green-800 dark:text-green-200 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        PayPal ist konfiguriert ({{ ucfirst($organization->paypal_mode) }})
                    </div>
                @endif
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('organizer.organization.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Logo Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Logo</h3>
            <div class="flex flex-col md:flex-row items-start gap-6">
                <div class="flex-shrink-0">
                    <div id="logoPreview" class="h-32 w-32 rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-600">
                        @if($organization->logo)
                            <img src="{{ asset('storage/'.$organization->logo) }}" class="w-full h-full object-cover" alt="Logo">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                                {{ $organization->initials() }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo hochladen</label>
                    <input type="file" name="logo" accept="image/*" id="logoInput" onchange="previewLogo(event)"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Empfohlen: Quadratisches Bild, mindestens 200x200px (PNG, JPG, max. 2MB)</p>
                    @error('logo')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    @if($organization->logo)
                        <div class="mt-3">
                            <button type="button" onclick="deleteLogo()"
                                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                                Logo entfernen
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grundinformationen -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Grundinformationen</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $organization->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung</label>
                    <textarea name="description" rows="4"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">{{ old('description', $organization->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Website</label>
                        <input type="url" name="website" value="{{ old('website', $organization->website) }}" placeholder="https://..."
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                        @error('website')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-Mail</label>
                        <input type="email" name="email" value="{{ old('email', $organization->email) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefon</label>
                        <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-100">
                        @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-center gap-3 pt-6">
            <a href="{{ route('organizer.organizations.select') }}"
               class="w-full sm:w-auto px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors text-center">
                Zurück zur Auswahl
            </a>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('organizer.organization.paypal') }}"
                   class="w-full sm:w-auto px-6 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium rounded-lg transition-colors text-center inline-flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.541c-.013.076-.026.175-.041.254-.93 4.778-4.005 7.201-9.138 7.201h-2.19a.563.563 0 0 0-.556.479l-1.187 7.527h-.506l.506-3.219 1.088-6.894a1.344 1.344 0 0 1 1.33-1.139h2.19c3.51 0 6.146-1.432 7.071-5.788.029-.14.054-.28.076-.421.018-.113.029-.226.038-.339a9.189 9.189 0 0 1 1.924.879z"/>
                    </svg>
                    PayPal-Einstellungen
                </a>
                <a href="{{ route('organizer.bank-account.index') }}"
                   class="w-full sm:w-auto px-6 py-2 border border-blue-600 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 font-medium rounded-lg transition-colors text-center">
                    Rechnungsdaten
                </a>
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Änderungen speichern
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Delete Logo Form (hidden) -->
<form id="deleteLogoForm" method="POST" action="{{ route('organizer.organization.delete-logo') }}" style="display:none;">
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
            preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover" alt="Logo Vorschau">';
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

