<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Log-Benachrichtigungen</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Verwalten Sie Benachrichtigungen für kritische System-Fehler</p>
            </div>
            <a href="{{ route('admin.system-logs.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                ← Zurück zu System Logs
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative dark:bg-green-900 dark:border-green-700 dark:text-green-200" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="space-y-6">
        <!-- Info Box -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Über Log-Benachrichtigungen</h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p class="mb-2">Benutzer mit der Permission "receive-critical-log-notifications" erhalten automatisch E-Mail-Benachrichtigungen bei kritischen System-Fehlern.</p>
                        <p class="mb-2"><strong>Kritische Log-Levels:</strong> ERROR, CRITICAL, ALERT, EMERGENCY</p>
                        <p><strong>Features:</strong></p>
                        <ul class="list-disc list-inside mt-1 ml-2">
                            <li>Automatische Duplicate-Detection (5 Minuten)</li>
                            <li>Gruppierung ähnlicher Fehler</li>
                            <li>E-Mail und Datenbank-Benachrichtigungen</li>
                            <li>Asynchrone Verarbeitung via Queue</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Notification -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Test-Benachrichtigung</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Senden Sie eine Test-Benachrichtigung an Ihre E-Mail-Adresse, um das System zu testen.
                </p>
                <form action="{{ route('admin.log-notifications.test') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Test-Benachrichtigung senden
                    </button>
                </form>
            </div>
        </div>

        <!-- Users with Permission -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                    Benutzer mit Benachrichtigungen ({{ $usersWithPermission->count() }})
                </h3>
            </div>
            <div class="p-6">
                @if($usersWithPermission->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                        Keine Benutzer haben derzeit die Berechtigung für Log-Benachrichtigungen.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">E-Mail</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rolle</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aktionen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($usersWithPermission as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            @if($user->hasRole('admin'))
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                    Benutzer
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <form action="{{ route('admin.log-notifications.revoke') }}" method="POST" class="inline" onsubmit="return confirm('Benachrichtigungen für {{ $user->name }} deaktivieren?')">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                    Deaktivieren
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

        <!-- Add Users -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Benutzer hinzufügen</h3>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.log-notifications.give') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Wählen Sie einen Administrator
                            </label>
                            <select name="user_id" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                <option value="">-- Benutzer auswählen --</option>
                                @foreach($allAdmins as $admin)
                                    @if(!$admin->hasPermissionTo('receive-critical-log-notifications'))
                                        <option value="{{ $admin->id }}">{{ $admin->name }} ({{ $admin->email }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Hinzufügen
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Benachrichtigungs-Statistiken</h3>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Aktive Empfänger</dt>
                        <dd class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $usersWithPermission->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Kritische Levels</dt>
                        <dd class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">4</dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">ERROR, CRITICAL, ALERT, EMERGENCY</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duplicate-Fenster</dt>
                        <dd class="mt-1 text-3xl font-bold text-gray-900 dark:text-gray-100">5 Min</dd>
                        <dd class="text-xs text-gray-500 dark:text-gray-400 mt-1">Verhindert Spam-Benachrichtigungen</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-layouts.app>

