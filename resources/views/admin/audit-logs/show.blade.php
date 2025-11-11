<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Audit Log Details') }}
            </h2>
            <a href="{{ route('admin.audit-logs.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Zurück zur Übersicht
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->id }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Datum/Zeit</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('d.m.Y H:i:s') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Benutzer</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($auditLog->user)
                                    {{ $auditLog->user->name }} ({{ $auditLog->user->email }})
                                @else
                                    System
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Aktion</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if(str_contains($auditLog->action, 'created')) bg-green-100 text-green-800
                                    @elseif(str_contains($auditLog->action, 'updated')) bg-blue-100 text-blue-800
                                    @elseif(str_contains($auditLog->action, 'deleted')) bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $auditLog->action }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Modell</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $auditLog->auditable_type ? class_basename($auditLog->auditable_type) : '-' }}
                                @if($auditLog->auditable_id)
                                    (ID: {{ $auditLog->auditable_id }})
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">IP-Adresse</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->ip_address ?? '-' }}</dd>
                        </div>

                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $auditLog->user_agent ?? '-' }}</dd>
                        </div>

                        @if($auditLog->description)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Beschreibung</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->description }}</dd>
                            </div>
                        @endif

                        @if($auditLog->old_values)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Alte Werte</dt>
                                <dd class="mt-1">
                                    <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </dd>
                            </div>
                        @endif

                        @if($auditLog->new_values)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Neue Werte</dt>
                                <dd class="mt-1">
                                    <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </dd>
                            </div>
                        @endif

                        @php
                            $changes = $auditLog->getChangesSummary();
                        @endphp

                        @if(count($changes) > 0)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Änderungen</dt>
                                <dd class="mt-1">
                                    <div class="bg-blue-50 border border-blue-200 rounded p-4">
                                        <table class="min-w-full">
                                            <thead>
                                                <tr>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Feld</th>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Alt</th>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Neu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($changes as $field => $change)
                                                    <tr class="border-t border-blue-200">
                                                        <td class="py-2 text-sm font-medium">{{ $field }}</td>
                                                        <td class="py-2 text-sm text-red-600">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</td>
                                                        <td class="py-2 text-sm text-green-600">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-6 flex justify-end space-x-2">
                        <form action="{{ route('admin.audit-logs.destroy', $auditLog) }}" method="POST" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Log-Eintrag löschen möchten?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Eintrag löschen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
<x-layouts.app>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Audit Log Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Detaillierte Ansicht des Log-Eintrags</p>
            </div>
            <a href="{{ route('admin.audit-logs.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                ← Zurück zur Übersicht
            </a>
        </div>
    </div>

    <div class="space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->id }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Datum/Zeit</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->created_at->format('d.m.Y H:i:s') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Benutzer</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($auditLog->user)
                                    {{ $auditLog->user->name }} ({{ $auditLog->user->email }})
                                @else
                                    System
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Aktion</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if(str_contains($auditLog->action, 'created')) bg-green-100 text-green-800
                                    @elseif(str_contains($auditLog->action, 'updated')) bg-blue-100 text-blue-800
                                    @elseif(str_contains($auditLog->action, 'deleted')) bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $auditLog->action }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Modell</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $auditLog->auditable_type ? class_basename($auditLog->auditable_type) : '-' }}
                                @if($auditLog->auditable_id)
                                    (ID: {{ $auditLog->auditable_id }})
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">IP-Adresse</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->ip_address ?? '-' }}</dd>
                        </div>

                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">User Agent</dt>
                            <dd class="mt-1 text-sm text-gray-900 break-all">{{ $auditLog->user_agent ?? '-' }}</dd>
                        </div>

                        @if($auditLog->description)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Beschreibung</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $auditLog->description }}</dd>
                            </div>
                        @endif

                        @if($auditLog->old_values)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Alte Werte</dt>
                                <dd class="mt-1">
                                    <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </dd>
                            </div>
                        @endif

                        @if($auditLog->new_values)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Neue Werte</dt>
                                <dd class="mt-1">
                                    <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </dd>
                            </div>
                        @endif

                        @php
                            $changes = $auditLog->getChangesSummary();
                        @endphp

                        @if(count($changes) > 0)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Änderungen</dt>
                                <dd class="mt-1">
                                    <div class="bg-blue-50 border border-blue-200 rounded p-4">
                                        <table class="min-w-full">
                                            <thead>
                                                <tr>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Feld</th>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Alt</th>
                                                    <th class="text-left text-xs font-medium text-gray-500 uppercase pb-2">Neu</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($changes as $field => $change)
                                                    <tr class="border-t border-blue-200">
                                                        <td class="py-2 text-sm font-medium">{{ $field }}</td>
                                                        <td class="py-2 text-sm text-red-600">{{ is_array($change['old']) ? json_encode($change['old']) : $change['old'] }}</td>
                                                        <td class="py-2 text-sm text-green-600">{{ is_array($change['new']) ? json_encode($change['new']) : $change['new'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-6 flex justify-end space-x-2">
                        <form action="{{ route('admin.audit-logs.destroy', $auditLog) }}" method="POST" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Log-Eintrag löschen möchten?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Eintrag löschen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

