@extends('layouts.app')

@section('title', 'Organisation wählen')

@section('content')
<div class="container py-6">
    <h1 class="text-2xl font-semibold mb-4">Organisation wählen</h1>

    @if(session('warning'))
        <div class="alert alert-warning mb-4">{{ session('warning') }}</div>
    @endif

    @if($organizations->isEmpty())
        <div class="card p-6">
            <p class="mb-4">Sie gehören noch keiner Organisation an.</p>
            <a href="{{ route('organizer.organizations.create') }}" class="btn btn-primary">Neue Organisation erstellen</a>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($organizations as $org)
                <div class="card p-4 flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        @if($org->logo)
                            <img src="{{ asset('storage/'.$org->logo) }}" alt="Logo" class="h-12 w-12 object-cover rounded">
                        @else
                            <div class="h-12 w-12 bg-gray-200 flex items-center justify-center rounded text-gray-600 font-bold">{{ $org->initials() }}</div>
                        @endif
                        <div>
                            <h2 class="font-semibold text-lg">{{ $org->name }}</h2>
                            <p class="text-sm text-gray-500">Rolle: {{ $org->getUserRole(auth()->user()) ?? 'Mitglied' }}</p>
                        </div>
                    </div>
                    <p class="text-sm text-gray-700 line-clamp-3">{{ $org->description }}</p>
                    <form method="POST" action="{{ route('organizer.organizations.switch', $org) }}">
                        @csrf
                        <button class="btn btn-primary w-full">Diese Organisation auswählen</button>
                    </form>
                </div>
            @endforeach
        </div>
        <div class="mt-8">
            <a href="{{ route('organizer.organizations.create') }}" class="btn btn-outline-primary">Weitere Organisation erstellen</a>
        </div>
    @endif
</div>
@endsection
