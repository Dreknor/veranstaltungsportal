<x-layouts.app title="Organisation: {{ $organization->name }}">
    <div class="min-h-screen bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        @if($organization->logo_path)
                            <img src="{{ asset('storage/' . $organization->logo_path) }}"
                                 alt="{{ $organization->name }}"
                                 class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                                {{ strtoupper(substr($organization->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="ml-4">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $organization->name }}</h1>
                            <p class="text-gray-600 mt-1">{{ $organization->email }}</p>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.organizations.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            ← Zurück zur Übersicht
                        </a>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Gesamt Events</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_events'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Veröffentlicht</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['published_events'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Kommende</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_events'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Buchungen</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organization Info -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Basic Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Grundinformationen</h2>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $organization->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">E-Mail</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $organization->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Telefon</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $organization->phone ?? 'Nicht angegeben' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Website</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($organization->website)
                                    <a href="{{ $organization->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        {{ $organization->website }}
                                    </a>
                                @else
                                    Nicht angegeben
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $organization->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $organization->is_active ? 'Aktiv' : 'Inaktiv' }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Erstellt am</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $organization->created_at->format('d.m.Y H:i') }} Uhr</dd>
                        </div>
                    </dl>
                </div>

                <!-- Fee Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Gebühren-Einstellungen</h2>
                        <a href="{{ route('admin.organizer-fees.edit', $organization) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="fas fa-edit mr-1"></i>Bearbeiten
                        </a>
                    </div>

                    @if($organization->custom_platform_fee && ($organization->custom_platform_fee['enabled'] ?? false))
                        @php
                            $customFee = $organization->custom_platform_fee;
                            $feeType = $customFee['fee_type'] ?? 'percentage';
                        @endphp
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center mb-2">
                                <svg class="h-5 w-5 text-purple-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path>
                                </svg>
                                <span class="text-sm font-medium text-purple-800">Individuelle Gebühren aktiv</span>
                            </div>
                        </div>

                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gebührentyp</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $feeType === 'percentage' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $feeType === 'percentage' ? 'Prozentual' : 'Festbetrag' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gebühr</dt>
                                <dd class="mt-1 text-sm font-bold text-gray-900">
                                    @if($feeType === 'percentage')
                                        {{ $customFee['fee_percentage'] ?? 0 }}%
                                    @else
                                        {{ number_format($customFee['fee_fixed_amount'] ?? 0, 2, ',', '.') }} €
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Mindestgebühr</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ number_format($customFee['minimum_fee'] ?? 0, 2, ',', '.') }} €
                                </dd>
                            </div>
                            @if(!empty($customFee['notes']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notizen</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $customFee['notes'] }}</dd>
                                </div>
                            @endif
                            @if(isset($customFee['updated_at']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Letzte Aktualisierung</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($customFee['updated_at'])->format('d.m.Y H:i') }} Uhr
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    @else
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-3">
                                Diese Organisation nutzt die Standard-Plattformgebühren.
                            </p>
                            <a href="{{ route('admin.organizer-fees.edit', $organization) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                Individuelle Gebühren festlegen
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Owner & Team -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Eigentümer & Team</h2>
                <div class="space-y-4">
                    <!-- Owner -->
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                {{ strtoupper(substr($organization->owner->name, 0, 1)) }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $organization->owner->name }}</div>
                                <div class="text-sm text-gray-500">{{ $organization->owner->email }}</div>
                            </div>
                        </div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            Eigentümer
                        </span>
                    </div>

                    <!-- Team Members -->
                    @foreach($organization->members->where('id', '!=', $organization->owner_id) as $member)
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 last:border-b-0">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-gray-400 to-gray-600 flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                </div>
                            </div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Mitglied
                            </span>
                        </div>
                    @endforeach

                    @if($organization->members->where('id', '!=', $organization->owner_id)->isEmpty())
                        <p class="text-sm text-gray-500 py-2">Keine weiteren Team-Mitglieder</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Aktionen</h2>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('admin.organizer-fees.edit', $organization) }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                        <i class="fas fa-dollar-sign mr-2"></i>
                        Gebühren verwalten
                    </a>

                    <form method="POST" action="{{ route('admin.organizations.toggle-active', $organization) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-{{ $organization->is_active ? 'ban' : 'check-circle' }} mr-2"></i>
                            {{ $organization->is_active ? 'Deaktivieren' : 'Aktivieren' }}
                        </button>
                    </form>

                    @if($stats['total_events'] === 0)
                        <form method="POST" action="{{ route('admin.organizations.destroy', $organization) }}"
                              onsubmit="return confirm('Organisation wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                <i class="fas fa-trash mr-2"></i>
                                Löschen
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
