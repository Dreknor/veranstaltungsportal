@extends('layouts.app')

@section('title', 'Neue Organisation erstellen')

@section('content')
<div class="container py-6 max-w-2xl">
    <h1 class="text-2xl font-semibold mb-6">Neue Organisation erstellen</h1>

    <form method="POST" action="{{ route('organizer.organizations.store') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        <div>
            <label class="form-label">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="form-label">Beschreibung</label>
            <textarea name="description" rows="4" class="form-textarea">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="form-label">Website</label>
                <input type="url" name="website" value="{{ old('website') }}" class="form-input">
                @error('website')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">E-Mail</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input">
                @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label class="form-label">Telefon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-input">
                @error('phone')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Logo (optional)</label>
                <input type="file" name="logo" class="form-input" accept="image/*">
                @error('logo')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex justify-end gap-4">
            <a href="{{ route('organizer.organizations.select') }}" class="btn btn-secondary">Abbrechen</a>
            <button class="btn btn-primary">Organisation erstellen</button>
        </div>
    </form>
</div>
@endsection
