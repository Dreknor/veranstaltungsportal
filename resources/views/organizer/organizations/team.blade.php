@extends('layouts.app')

@section('title', 'Team verwalten')

@section('content')
<div class="container py-6 max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold">Team der Organisation</h1>
        <a href="{{ route('organizer.organization.edit') }}" class="btn btn-outline-primary">Organisation bearbeiten</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    <div class="card p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">Mitglied einladen</h2>
        <form method="POST" action="{{ route('organizer.team.invite') }}" class="grid md:grid-cols-3 gap-4 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="form-label">E-Mail des Benutzers</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                @error('email')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Rolle</label>
                <select name="role" class="form-select" required>
                    <option value="admin" @selected(old('role')==='admin')>Administrator</option>
                    <option value="member" @selected(old('role')==='member')>Mitglied</option>
                </select>
                @error('role')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-3 flex justify-end">
                <button class="btn btn-primary">Einladen / Hinzufügen</button>
            </div>
        </form>
    </div>

    <div class="card p-6">
        <h2 class="text-lg font-semibold mb-4">Mitglieder</h2>
        <table class="table-auto w-full text-left">
            <thead>
                <tr class="border-b">
                    <th class="py-2">Name</th>
                    <th class="py-2">E-Mail</th>
                    <th class="py-2">Rolle</th>
                    <th class="py-2">Beigetreten</th>
                    <th class="py-2 text-right">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    <tr class="border-b">
                        <td class="py-2 flex items-center gap-2">
                            <img src="{{ $member->profilePhotoUrl() }}" class="h-8 w-8 rounded-full" alt="Profil">
                            {{ $member->fullName() }}
                        </td>
                        <td class="py-2">{{ $member->email }}</td>
                        <td class="py-2">
                            <form method="POST" action="{{ route('organizer.team.update-role', ['user' => $member->id]) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PUT')
                                <select name="role" class="form-select form-select-sm">
                                    <option value="owner" @selected($member->pivot->role==='owner')>Owner</option>
                                    <option value="admin" @selected($member->pivot->role==='admin')>Admin</option>
                                    <option value="member" @selected($member->pivot->role==='member')>Mitglied</option>
                                </select>
                                <button class="btn btn-sm btn-outline-primary">Speichern</button>
                            </form>
                        </td>
                        <td class="py-2 text-sm">{{ optional($member->pivot->joined_at)->format('d.m.Y') }}</td>
                        <td class="py-2 text-right">
                            @if($member->pivot->role !== 'owner')
                                <form method="POST" action="{{ route('organizer.team.remove', ['user' => $member->id]) }}" onsubmit="return confirm('Mitglied wirklich entfernen?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Entfernen</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-500">Haupt-Inhaber</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">Noch keine Mitglieder.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
