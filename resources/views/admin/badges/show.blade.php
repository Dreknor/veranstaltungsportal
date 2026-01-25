<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Badge Details: {{ $badge->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.badges.edit', $badge) }}" class="btn-primary">
                    <i class="fas fa-edit mr-2"></i>
                    Bearbeiten
                </a>
                <a href="{{ route('admin.badges.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Zurück
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Badge Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6">
                        <div class="text-center mb-6">
                            <div class="inline-flex w-24 h-24 rounded-full items-center justify-center text-5xl mb-4"
                                 style="background-color: {{ $badge->color }}20; color: {{ $badge->color }}">
                                @if($badge->image_path)
                                    <img src="{{ asset($badge->image_path) }}"
                                         alt="{{ $badge->name }}"
                                         class="w-24 h-24 rounded-full object-cover border-4"
                                         style="border-color: {{ $badge->color }}">
                                @else
                                    <i class="{{ $badge->icon }}"></i>
                                @endif
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $badge->name }}</h3>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                @if($badge->type === 'attendance') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                @elseif($badge->type === 'achievement') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                @endif">
                                {{ ucfirst($badge->type) }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung</label>
                                <p class="text-gray-900 dark:text-white">{{ $badge->description }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Punkte</label>
                                <p class="text-gray-900 dark:text-white">
                                    <i class="fas fa-star text-yellow-500 mr-1"></i>
                                    {{ $badge->points }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Anforderung</label>
                                <p class="text-gray-900 dark:text-white">
                                    {{ $badge->requirement_value }} {{ str_replace('_', ' ', $badge->requirement_type) }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vergeben</label>
                                <p class="text-gray-900 dark:text-white">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $badge->users->count() }} mal
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Award Badge Form -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg p-6 mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Badge manuell vergeben</h4>
                        <form method="POST" action="{{ route('admin.badges.award', $badge) }}">
                            @csrf
                            <div class="mb-4">
                                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Benutzer auswählen</label>
                                <select name="user_id" id="user_id" class="input" required>
                                    <option value="">Benutzer auswählen...</option>
                                    @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn-primary w-full">
                                <i class="fas fa-award mr-2"></i>
                                Badge vergeben
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Awarded Users -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                Benutzer mit diesem Badge ({{ $badge->users->count() }})
                            </h4>

                            @if($badge->users->isEmpty())
                                <div class="text-center py-12">
                                    <i class="fas fa-users text-6xl text-gray-400 dark:text-gray-600 mb-4"></i>
                                    <p class="text-gray-500 dark:text-gray-400">Dieser Badge wurde noch nicht vergeben.</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Benutzer</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Email</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Erhalten am</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aktionen</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($badge->users as $user)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <img src="{{ $user->profilePhotoUrl() }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-3">
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                                @if($user->is_organizer)
                                                                    <span class="text-xs text-blue-600 dark:text-blue-400">Veranstalter</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ $user->email }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                        {{ $user->pivot->earned_at->format('d.m.Y H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                        <form method="POST" action="{{ route('admin.badges.revoke', $badge) }}" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Badge entfernen möchten?')">
                                                            @csrf
                                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                                <i class="fas fa-times mr-1"></i>
                                                                Entfernen
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

