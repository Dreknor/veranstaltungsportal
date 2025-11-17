@extends('layouts.app')

@section('title', 'Organisation bearbeiten')

@section('content')
<div class="container py-6 max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Organisation bearbeiten</h1>
        <a href="{{ route('organizer.team.index') }}" class="btn btn-outline-primary">Team verwalten</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('organizer.organization.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Logo Upload Section -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold mb-4">Logo</h3>
            <div class="flex items-start gap-6">
                <div class="flex-shrink-0">
                    <div id="logoPreview" class="h-32 w-32 rounded-lg overflow-hidden border-2 border-gray-200">
                        @if($organization->logo)
                            <img src="{{ asset('storage/'.$organization->logo) }}" class="w-full h-full object-cover" alt="Logo">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-3xl font-bold">
                                {{ $organization->initials() }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex-1">
                    <label class="form-label">Logo hochladen</label>
                    <input type="file" name="logo" accept="image/*" class="form-input" id="logoInput" onchange="previewLogo(event)">
                    <p class="text-sm text-gray-500 mt-1">Empfohlen: Quadratisches Bild, mindestens 200x200px (PNG, JPG, max. 2MB)</p>
                    @error('logo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror

                    @if($organization->logo)
                        <div class="mt-3">
                            <button type="button" onclick="deleteLogo()" class="btn btn-sm btn-danger">
                                Logo entfernen
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="card p-6 space-y-4">
            <h3 class="text-lg font-semibold">Grundinformationen</h3>

            <div>
                <label class="form-label">Name *</label>
                <input type="text" name="name" value="{{ old('name', $organization->name) }}" class="form-input" required>
                @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Beschreibung</label>
                <textarea name="description" rows="4" class="form-textarea">{{ old('description', $organization->description) }}</textarea>
                @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Website</label>
                    <input type="url" name="website" value="{{ old('website', $organization->website) }}" class="form-input" placeholder="https://...">
                    @error('website')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">E-Mail</label>
                    <input type="email" name="email" value="{{ old('email', $organization->email) }}" class="form-input">
                    @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Telefon</label>
                    <input type="text" name="phone" value="{{ old('phone', $organization->phone) }}" class="form-input">
                    @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center">
            <a href="{{ route('organizer.organizations.select') }}" class="btn btn-secondary">Zurück zur Auswahl</a>
            <div class="flex gap-3">
                <a href="{{ route('organizer.bank-account.index') }}" class="btn btn-outline-primary">Rechnungsdaten</a>
                <button type="submit" class="btn btn-primary">Änderungen speichern</button>
            </div>
        </div>
    </form>
</div>

<!-- Delete Logo Form (hidden) -->
<form id="deleteLogoForm" method="POST" action="{{ route('organizer.organization.delete-logo') }}" style="display:none;">
    @csrf
    @method('DELETE')
</form>

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
@endsection

