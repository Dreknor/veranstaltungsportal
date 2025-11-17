@extends('layouts.app')

@section('title', 'Team-Mitglieder importieren')

@section('content')
<div class="container py-6 max-w-3xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Team-Mitglieder importieren</h1>
        <a href="{{ route('organizer.team.index') }}" class="btn btn-secondary">Zurück zum Team</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
            @if(session('import_errors') && count(session('import_errors')) > 0)
                <details class="mt-2">
                    <summary class="cursor-pointer text-sm">Fehler anzeigen ({{ count(session('import_errors')) }})</summary>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </details>
            @endif
        </div>
    @endif

    <div class="card p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">📋 Anleitung</h2>
        <ol class="list-decimal list-inside space-y-2 text-gray-700">
            <li>Laden Sie die CSV-Vorlage herunter</li>
            <li>Tragen Sie die E-Mail-Adressen und Rollen der Mitglieder ein</li>
            <li>Speichern Sie die Datei im CSV-Format</li>
            <li>Laden Sie die Datei hier hoch</li>
        </ol>

        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800 mb-2"><strong>Wichtig:</strong></p>
            <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                <li>Die Benutzer müssen bereits registriert sein</li>
                <li>Rollen können sein: <code class="bg-blue-100 px-1 rounded">admin</code> oder <code class="bg-blue-100 px-1 rounded">member</code></li>
                <li>Bereits vorhandene Mitglieder werden übersprungen</li>
                <li>Alle importierten Mitglieder erhalten eine E-Mail-Benachrichtigung</li>
            </ul>
        </div>

        <a href="{{ route('organizer.team.import.template') }}" class="btn btn-primary mt-4">
            📥 CSV-Vorlage herunterladen
        </a>
    </div>

    <div class="card p-6">
        <h2 class="text-lg font-semibold mb-4">CSV-Datei hochladen</h2>

        <form method="POST" action="{{ route('organizer.team.import.process') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="form-label">CSV-Datei *</label>
                <input type="file" name="csv_file" accept=".csv,.txt" class="form-input" required>
                <p class="text-sm text-gray-500 mt-1">Format: CSV mit Komma als Trennzeichen, max. 2MB</p>
                @error('csv_file')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Standard-Rolle (falls nicht in CSV angegeben)</label>
                <select name="default_role" class="form-select" required>
                    <option value="member" selected>Member (eingeschränkte Berechtigungen)</option>
                    <option value="admin">Admin (volle Berechtigungen)</option>
                </select>
                @error('default_role')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('organizer.team.index') }}" class="btn btn-secondary">Abbrechen</a>
                <button type="submit" class="btn btn-primary">Importieren</button>
            </div>
        </form>
    </div>

    <div class="card p-6 mt-6 bg-gray-50">
        <h3 class="font-semibold mb-2">💡 Beispiel CSV-Format:</h3>
        <pre class="bg-white p-4 rounded border text-sm">
            <code>email,role
max@example.com,member
sarah@example.com,admin
tim@example.com,member</code></pre>
    </div>
</div>
@endsection

