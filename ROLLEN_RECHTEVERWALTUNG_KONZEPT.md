# Konzept: Vollständige Rollen- & Rechteverwaltung

**Projekt:** Veranstaltungsportal  
**Stand:** Laravel 12, PHP 8.3, Spatie/laravel-permission  
**Erstellt:** 23.02.2026  

---

## 1. Zusammenfassung / Ziel

Das Ziel ist eine vollständige, administrierbare Rollen- und Rechteverwaltung im Admin-Bereich. Administratoren sollen eigene Rollen anlegen, benennen, mit Berechtigungen versehen und löschen können. Ebenso sollen neue Permissions erstellt, Gruppen zugeordnet und bestehende Permissions verwaltet werden können. System-kritische Rollen (`admin`, `user`) und Permissions sind gegen versehentliches Löschen geschützt.

**Kernfunktionen (neu):**
- Rollen erstellen / umbenennen / löschen
- Permissions erstellen / umbenennen / löschen
- Permission-Gruppen pflegen
- Rollen × Permissions-Matrix als Übersicht
- Schutz für System-Rollen und System-Permissions
- Audit-Logging aller Änderungen

---

## 2. Ist-Zustand

### Existierende Infrastruktur

| Komponente | Datei | Status |
|---|---|---|
| Spatie Permission Package | `composer.json` | ✅ installiert |
| Permission-Tabellen | `2025_11_07_160052_create_permission_tables.php` | ✅ migriert |
| 5 feste Rollen | `RolesAndPermissionsSeeder.php` | ✅ vorhanden |
| 33 Permissions | `RolesAndPermissionsSeeder.php` | ✅ vorhanden |
| Controller (index, edit, update) | `RoleManagementController.php` | ✅ vorhanden |
| Views (index, edit) | `resources/views/admin/roles/` | ✅ vorhanden |
| Routen (GET index, GET edit, PUT update) | `routes/web.php` | ✅ vorhanden |

### Bestehende Rollen

| Name | Beschreibung | Permissions |
|---|---|---|
| `admin` | Vollzugriff | Alle 33+ |
| `organizer` | Veranstaltungsverwaltung | ~15 |
| `user` | Basiszugriff | ~6 |
| `moderator` | Content-Moderation | ~5 |
| `viewer` | Nur-Lesen | ~4 |

### Bestehende Datenbankstruktur (Spatie)

```
permissions:          id, name, guard_name, created_at, updated_at
roles:                id, name, guard_name, created_at, updated_at
model_has_permissions: permission_id, model_type, model_id
model_has_roles:       role_id, model_type, model_id
role_has_permissions:  permission_id, role_id
```

### Was fehlt

- **Controller:** `store()`, `destroy()`, `storePermission()`, `destroyPermission()`, `updatePermission()`, `matrix()`
- **Views:** `create.blade.php`, `matrix.blade.php`, `permissions/index.blade.php`, `permissions/create.blade.php`
- **Routen:** POST `/roles`, DELETE `/roles/{role}`, POST `/permissions`, DELETE `/permissions/{permission}`, PUT `/permissions/{permission}`, GET `/roles/matrix`
- **DB:** Keine `description`, `color`, `is_system`-Felder in `roles`; keine `group`, `description`-Felder in `permissions`
- **Schutz:** Kein Schutz gegen Löschen von System-Rollen
- **Audit-Logging:** Keine Protokollierung von Permission-Änderungen

---

## 3. Soll-Zustand

### 3.1 Rollen-Verwaltung

- **Neue Rolle anlegen:** Name (eindeutig, lowercase, nur a-z und Bindestrich), Beschreibung, Farbe (Badge-Farbe für UI), Permissions sofort zuweisbar
- **Rolle bearbeiten:** Name ändern (mit Validierung, System-Rollen nicht umbenennbar), Beschreibung und Farbe ändern, Permissions sync
- **Rolle löschen:** Nur benutzerdefinierte Rollen (nicht `admin`, `user`, `organizer`, `moderator`, `viewer`). Benutzer, die nur diese Rolle haben, werden auf `user` zurückgesetzt (oder Löschen wird geblockt mit Hinweis)
- **Übersicht:** Anzahl Benutzer, Anzahl Permissions, Erstellt-Datum, Is-System-Badge

### 3.2 Permissions-Verwaltung

- **Neue Permission anlegen:** Name (z.B. `manage invoices`), Gruppe (z.B. `invoices`), Beschreibung, Guard (default: `web`)
- **Permission bearbeiten:** Name, Gruppe, Beschreibung ändern
- **Permission löschen:** Nur nicht-system-kritische Permissions. Warnung wenn Permission noch Rollen zugewiesen ist
- **Gruppen-Verwaltung:** Gruppe ist eine freie Zeichenkette; alle Permissions mit gleicher Gruppe werden zusammengefasst

### 3.3 Matrix-Ansicht

Eine Tabelle mit Rollen als Spalten und Permissions (gruppiert) als Zeilen. Checkboxen können direkt in der Matrix umgeschaltet werden (per AJAX oder Form-Submit).

### 3.4 Schutz-Mechanismen

- System-Rollen (`is_system = true`): `admin`, `user`, `organizer`, `moderator`, `viewer` → nicht löschbar, Name nicht änderbar
- System-Permissions: Core-Permissions (z.B. `access admin panel`) → nicht löschbar
- Admin-Rolle behält immer alle Permissions (kann nicht auf 0 Permissions reduziert werden)
- Wenn eine Rolle gelöscht wird: Benutzer zählen, Warnung anzeigen, Fallback-Rolle zuweisen

---

## 4. Datenbankänderungen

### 4.1 Migration: Metadaten für Rollen

**Datei:** `database/migrations/YYYY_MM_DD_000001_add_meta_to_roles_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->string('color')->default('#6b7280')->after('description'); // Tailwind-Farbe oder Hex
            $table->boolean('is_system')->default(false)->after('color');
        });

        // Bestehende System-Rollen markieren
        $systemRoles = ['admin', 'user', 'organizer', 'moderator', 'viewer'];
        Role::whereIn('name', $systemRoles)->update(['is_system' => true]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'color', 'is_system']);
        });
    }
};
```

### 4.2 Migration: Metadaten für Permissions

**Datei:** `database/migrations/YYYY_MM_DD_000002_add_meta_to_permissions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group')->default('general')->after('name');
            $table->string('description')->nullable()->after('group');
            $table->boolean('is_system')->default(false)->after('description');
        });

        // Bestehende Permissions automatisch gruppieren (aus dem Namen extrahieren)
        // z.B. "view events" → group = "events"
        Permission::all()->each(function ($permission) {
            $parts = explode(' ', $permission->name);
            $group = count($parts) >= 2 ? $parts[1] : 'general';
            $permission->update(['group' => $group, 'is_system' => true]);
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['group', 'description', 'is_system']);
        });
    }
};
```

### 4.3 Eigene Model-Klassen (Spatie überschreiben)

Damit die neuen Felder in Models verfügbar sind, müssen eigene Model-Klassen angelegt und in `config/permission.php` registriert werden:

**`app/Models/Role.php`** (neu anlegen, falls nicht vorhanden):
```php
<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'description', 'color', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
```

**`app/Models/Permission.php`** (neu anlegen, falls nicht vorhanden):
```php
<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = ['name', 'guard_name', 'group', 'description', 'is_system'];

    protected $casts = [
        'is_system' => 'boolean',
    ];
}
```

**`config/permission.php`** – Models anpassen:
```php
'models' => [
    'permission' => App\Models\Permission::class,
    'role'       => App\Models\Role::class,
],
```

---

## 5. Backend-Änderungen

### 5.1 RoleManagementController – neue Methoden

**Datei:** `app/Http/Controllers/Admin/RoleManagementController.php`

```php
/**
 * Neue Rolle anlegen (Formular anzeigen)
 */
public function create()
{
    $permissions = Permission::all()->groupBy('group');
    return view('admin.roles.create', compact('permissions'));
}

/**
 * Neue Rolle speichern
 */
public function store(Request $request)
{
    $validated = $request->validate([
        'name'        => 'required|string|lowercase|regex:/^[a-z0-9\-]+$/|unique:roles,name|max:50',
        'description' => 'nullable|string|max:255',
        'color'       => 'nullable|string|max:20',
        'permissions' => 'nullable|array',
        'permissions.*' => 'exists:permissions,name',
    ]);

    $role = Role::create([
        'name'        => $validated['name'],
        'guard_name'  => 'web',
        'description' => $validated['description'] ?? null,
        'color'       => $validated['color'] ?? '#6b7280',
        'is_system'   => false,
    ]);

    $role->syncPermissions($validated['permissions'] ?? []);

    // Audit Log
    activity()->log("Rolle '{$role->name}' wurde erstellt");

    return redirect()->route('admin.roles.index')
        ->with('success', "Rolle '{$role->name}' wurde erfolgreich erstellt.");
}

/**
 * Rolle löschen
 */
public function destroy(Role $role)
{
    if ($role->is_system) {
        return back()->with('error', "System-Rollen können nicht gelöscht werden.");
    }

    $userCount = $role->users()->count();
    if ($userCount > 0) {
        // Benutzer auf 'user'-Rolle zurücksetzen
        $fallbackRole = Role::where('name', 'user')->first();
        foreach ($role->users as $user) {
            $user->removeRole($role);
            if ($user->roles->isEmpty()) {
                $user->assignRole($fallbackRole);
            }
        }
    }

    $roleName = $role->name;
    $role->delete();

    app(PermissionRegistrar::class)->forgetCachedPermissions();
    activity()->log("Rolle '{$roleName}' wurde gelöscht");

    return redirect()->route('admin.roles.index')
        ->with('success', "Rolle '{$roleName}' wurde gelöscht. {$userCount} Benutzer wurden auf 'user' zurückgesetzt.");
}

/**
 * Matrix: Alle Rollen × Permissions
 */
public function matrix()
{
    $roles = Role::with('permissions')->get();
    $permissions = Permission::all()->groupBy('group');
    return view('admin.roles.matrix', compact('roles', 'permissions'));
}
```

### 5.2 PermissionManagementController (neu)

**Datei:** `app/Http/Controllers/Admin/PermissionManagementController.php`

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class PermissionManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $permissions = Permission::with('roles')
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        $groups = Permission::distinct()->pluck('group')->sort()->values();

        return view('admin.permissions.index', compact('permissions', 'groups'));
    }

    public function create()
    {
        $groups = Permission::distinct()->pluck('group')->sort()->values();
        return view('admin.permissions.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|regex:/^[a-z0-9 \-]+$/|unique:permissions,name|max:100',
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $permission = Permission::create([
            'name'        => $validated['name'],
            'guard_name'  => 'web',
            'group'       => strtolower($validated['group']),
            'description' => $validated['description'] ?? null,
            'is_system'   => false,
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Admin-Rolle bekommt automatisch alle neuen Permissions
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        activity()->log("Permission '{$permission->name}' wurde erstellt");

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '{$permission->name}' wurde erstellt.");
    }

    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->pluck('group')->sort()->values();
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'        => "required|string|regex:/^[a-z0-9 \-]+$/|unique:permissions,name,{$permission->id}|max:100",
            'group'       => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        // System-Permissions: nur Beschreibung und Gruppe änderbar, nicht der Name
        if ($permission->is_system) {
            $permission->update([
                'group'       => strtolower($validated['group']),
                'description' => $validated['description'] ?? null,
            ]);
        } else {
            $permission->update([
                'name'        => $validated['name'],
                'group'       => strtolower($validated['group']),
                'description' => $validated['description'] ?? null,
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission aktualisiert.");
    }

    public function destroy(Permission $permission)
    {
        if ($permission->is_system) {
            return back()->with('error', "System-Permissions können nicht gelöscht werden.");
        }

        $roleCount = $permission->roles()->count();
        $permissionName = $permission->name;
        $permission->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        activity()->log("Permission '{$permissionName}' wurde gelöscht");

        return back()->with('success', "Permission '{$permissionName}' gelöscht. War {$roleCount} Rolle(n) zugewiesen.");
    }
}
```

### 5.3 RoleService (optional, für komplexe Logik)

**Datei:** `app/Services/RoleService.php`

```php
<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleService
{
    /**
     * Prüft ob eine Rolle gelöscht werden darf
     */
    public function canDelete(Role $role): bool
    {
        return !$role->is_system;
    }

    /**
     * Prüft ob der Name einer Rolle geändert werden darf
     */
    public function canRename(Role $role): bool
    {
        return !$role->is_system;
    }

    /**
     * Löscht eine Rolle und setzt betroffene Benutzer zurück
     * Gibt Anzahl betroffener Benutzer zurück
     */
    public function deleteRole(Role $role): int
    {
        $fallback = Role::where('name', 'user')->first();
        $affected = 0;

        foreach ($role->users as $user) {
            $user->removeRole($role);
            if ($user->roles->isEmpty() && $fallback) {
                $user->assignRole($fallback);
                $affected++;
            }
        }

        $role->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $affected;
    }

    /**
     * Stellt sicher, dass Admin-Rolle immer alle Permissions hat
     */
    public function syncAdminPermissions(): void
    {
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->syncPermissions(Permission::all());
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }
}
```

---

## 6. Frontend-Änderungen

### 6.1 Neue Views-Übersicht

| View-Datei | Zweck |
|---|---|
| `admin/roles/create.blade.php` | Formular: Neue Rolle anlegen |
| `admin/roles/matrix.blade.php` | Rollen × Permissions Matrix |
| `admin/permissions/index.blade.php` | Alle Permissions mit Gruppen |
| `admin/permissions/create.blade.php` | Neue Permission anlegen |
| `admin/permissions/edit.blade.php` | Permission bearbeiten |

### 6.2 Anpassungen bestehender Views

**`admin/roles/index.blade.php`:**
- Button „Neue Rolle erstellen" hinzufügen (oben rechts)
- „Löschen"-Button je Rolle (nur wenn `!$role->is_system`)
- System-Rollen-Badge (🔒 Symbol)
- Link zur Matrix-Ansicht
- Farb-Badge dynamisch aus `$role->color` statt hartkodierter Klassen

**`admin/roles/edit.blade.php`:**
- Felder für `description` und `color` ergänzen
- Name-Feld sperren wenn `$role->is_system`
- „Rolle löschen"-Button (nur wenn `!$role->is_system`)

### 6.3 Beispiel: Neue Rolle anlegen (`create.blade.php`)

```blade
<x-layouts.app title="Neue Rolle erstellen">
    <div class="max-w-4xl mx-auto py-12 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Neue Rolle erstellen</h1>
            <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-800">← Zurück</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Rollenname <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="z.B. content-editor"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Nur Kleinbuchstaben, Zahlen und Bindestriche</p>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Beschreibung -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Beschreibung</label>
                    <textarea name="description" rows="2"
                              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">{{ old('description') }}</textarea>
                </div>

                <!-- Farbe -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Badge-Farbe</label>
                    <div class="flex gap-3 flex-wrap">
                        @foreach(['#6b7280','#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#ec4899'] as $color)
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="{{ $color }}"
                                       {{ old('color', '#6b7280') === $color ? 'checked' : '' }} class="sr-only">
                                <span class="block w-8 h-8 rounded-full border-4 border-transparent ring-2 ring-offset-2 ring-transparent hover:ring-gray-400"
                                      style="background-color: {{ $color }};"></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Permissions -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Berechtigungen zuweisen</h3>
                    @foreach($permissions as $group => $groupPermissions)
                        <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $group }}</h4>
                                <button type="button" class="text-xs text-blue-600 hover:text-blue-800 select-all-btn"
                                        data-group="{{ $group }}">Alle wählen</button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-start gap-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                               class="mt-0.5 rounded border-gray-300 text-blue-600 perm-{{ $group }}">
                                        <div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $permission->name }}</span>
                                            @if($permission->description)
                                                <p class="text-xs text-gray-500">{{ $permission->description }}</p>
                                            @endif
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('admin.roles.index') }}"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Abbrechen</a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Rolle erstellen</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
```

### 6.4 Beispiel: Matrix-Ansicht (`matrix.blade.php`)

```blade
<x-layouts.app title="Berechtigungs-Matrix">
    <div class="py-12 px-4">
        <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-gray-100">Berechtigungs-Matrix</h1>

        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-64">Permission</th>
                        @foreach($roles as $role)
                            <th class="px-4 py-3 text-center text-xs font-medium uppercase"
                                style="color: {{ $role->color }}">
                                {{ ucfirst($role->name) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($permissions as $group => $groupPermissions)
                        <tr class="bg-gray-100 dark:bg-gray-600">
                            <td colspan="{{ $roles->count() + 1 }}"
                                class="px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
                                {{ $group }}
                            </td>
                        </tr>
                        @foreach($groupPermissions as $permission)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-750">
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $permission->name }}
                                    @if($permission->description)
                                        <span class="block text-xs text-gray-400">{{ $permission->description }}</span>
                                    @endif
                                </td>
                                @foreach($roles as $role)
                                    <td class="px-4 py-2 text-center">
                                        @if($role->hasPermissionTo($permission->name))
                                            <span class="text-green-500">
                                                <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                        @else
                                            <span class="text-gray-300 dark:text-gray-600">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
```

---

## 7. Routen-Änderungen

**Datei:** `routes/web.php` – innerhalb der bestehenden Admin-Gruppe

```php
// Role & Permission Management (erweitert)
Route::get('/roles',               [RoleManagementController::class, 'index'])->name('roles.index');
Route::get('/roles/matrix',        [RoleManagementController::class, 'matrix'])->name('roles.matrix');
Route::get('/roles/create',        [RoleManagementController::class, 'create'])->name('roles.create');
Route::post('/roles',              [RoleManagementController::class, 'store'])->name('roles.store');
Route::get('/roles/{role}/edit',   [RoleManagementController::class, 'edit'])->name('roles.edit');
Route::put('/roles/{role}',        [RoleManagementController::class, 'update'])->name('roles.update');
Route::delete('/roles/{role}',     [RoleManagementController::class, 'destroy'])->name('roles.destroy');

// Permissions (neue eigenständige Routen)
Route::get('/permissions',             [PermissionManagementController::class, 'index'])->name('permissions.index');
Route::get('/permissions/create',      [PermissionManagementController::class, 'create'])->name('permissions.create');
Route::post('/permissions',            [PermissionManagementController::class, 'store'])->name('permissions.store');
Route::get('/permissions/{permission}/edit', [PermissionManagementController::class, 'edit'])->name('permissions.edit');
Route::put('/permissions/{permission}', [PermissionManagementController::class, 'update'])->name('permissions.update');
Route::delete('/permissions/{permission}', [PermissionManagementController::class, 'destroy'])->name('permissions.destroy');
```

> **Wichtig:** `GET /roles/matrix` und `GET /roles/create` müssen **vor** `GET /roles/{role}/edit` stehen, damit Laravel `matrix` und `create` nicht als `{role}`-Parameter interpretiert.

---

## 8. Sicherheitsaspekte

### 8.1 System-Rollen-Schutz

```php
// Im Controller – vor jeder destruktiven Operation prüfen:
if ($role->is_system) {
    abort(403, 'System-Rollen können nicht verändert werden.');
}
```

Die Felder `name` und `is_system` der System-Rollen dürfen im Frontend nicht als editierbare Felder erscheinen:

```blade
@if($role->is_system)
    <input type="text" value="{{ $role->name }}" disabled class="bg-gray-100 cursor-not-allowed ...">
    <span class="text-xs text-gray-500">🔒 System-Rolle – Name nicht änderbar</span>
@else
    <input type="text" name="name" value="{{ old('name', $role->name) }}" ...>
@endif
```

### 8.2 Admin-Rolle Vollzugriff sicherstellen

Nach jeder Permission-Änderung (neue Permission erstellt, Permission gelöscht) soll sichergestellt werden, dass die Admin-Rolle immer alle Permissions hat. Dies erfolgt im `PermissionManagementController::store()` automatisch:

```php
// Neue Permission → Admin-Rolle automatisch zuweisen
$adminRole = Role::where('name', 'admin')->first();
$adminRole?->givePermissionTo($permission);
```

### 8.3 Audit-Logging

Alle Änderungen an Rollen und Permissions werden in der Audit-Log-Tabelle protokolliert (sofern `owen-it/laravel-auditing` oder ähnliches vorhanden). Mindest-Logging mit dem bereits vorhandenen Log-System:

```php
activity()
    ->causedBy(auth()->user())
    ->withProperties(['role' => $role->name, 'permissions' => $validated['permissions'] ?? []])
    ->log('Rolle bearbeitet');
```

### 8.4 Benutzer-Schutz beim Rollen-Löschen

Bevor eine Rolle gelöscht wird, immer zählen wie viele Benutzer betroffen sind und eine Bestätigung anfordern:

```blade
<form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
      onsubmit="return confirm('Diese Rolle hat {{ $role->users_count }} Benutzer. Fortfahren?')">
    @csrf @method('DELETE')
    <button type="submit" class="...">Löschen</button>
</form>
```

### 8.5 CSRF & Validierung

Alle schreibenden Operationen sind durch `@csrf` geschützt. Alle Eingaben werden serverseitig validiert. Insbesondere:
- Rollen-Name: `regex:/^[a-z0-9\-]+$/` (keine Sonderzeichen, keine Leerzeichen)
- Permission-Name: `regex:/^[a-z0-9 \-]+$/` (Leerzeichen erlaubt, da Konvention z.B. `view events`)

---

## 9. Implementierungsreihenfolge

### Phase 1 – Datenbankgrundlage (Priorität: 🔴 Hoch)

1. `App\Models\Role` und `App\Models\Permission` als eigene Klassen anlegen
2. `config/permission.php` anpassen (eigene Models registrieren)
3. Migration `add_meta_to_roles_table` erstellen und ausführen
4. Migration `add_meta_to_permissions_table` erstellen und ausführen
5. `php artisan migrate`
6. `php artisan permission:cache-reset`

### Phase 2 – Backend (Priorität: 🔴 Hoch)

7. `RoleManagementController` um `create()`, `store()`, `destroy()`, `matrix()` erweitern
8. `PermissionManagementController` neu erstellen
9. Routen in `web.php` ergänzen
10. `RoleService` anlegen (optional, aber empfohlen für saubere Trennung)

### Phase 3 – Frontend (Priorität: 🟡 Mittel)

11. `admin/roles/index.blade.php` anpassen (Button „Neue Rolle", Lösch-Button, System-Badge)
12. `admin/roles/create.blade.php` neu anlegen
13. `admin/roles/edit.blade.php` anpassen (description, color, Schutz für System-Rollen)
14. `admin/roles/matrix.blade.php` neu anlegen
15. `admin/permissions/index.blade.php` neu anlegen
16. `admin/permissions/create.blade.php` neu anlegen
17. `admin/permissions/edit.blade.php` neu anlegen

### Phase 4 – Sicherheit & Polish (Priorität: 🟡 Mittel)

18. Audit-Logging in alle Controller-Methoden integrieren
19. Bestätigungs-Dialoge für Lösch-Operationen (JS `confirm()` oder Modal)
20. Navigation im Admin-Bereich um Permission-Link ergänzen
21. `php artisan permission:cache-reset` nach jeder Migration sicherstellen

### Phase 5 – Tests (Priorität: 🟢 Normal)

22. Feature-Tests für `RoleManagementController` (store, destroy)
23. Feature-Tests für `PermissionManagementController`
24. Test: System-Rollen können nicht gelöscht werden
25. Test: Admin-Rolle hat nach neuer Permission automatisch Zugriff
26. Test: Benutzer werden bei Rollen-Löschen korrekt auf Fallback gesetzt

---

## 10. Offene Fragen / Entscheidungspunkte

| Thema | Option A | Option B |
|---|---|---|
| Rollen-Namen änderbar? | Nur benutzerdefinierte Rollen | Gar keine (nur Beschreibung/Farbe) |
| Fallback bei Rollen-Löschen | Immer auf `user` setzen | Admin wählt Fallback-Rolle |
| Permission-Gruppen | Freier Text im Formular | Dropdown aus vorhandenen Gruppen |
| Matrix-Update | Nur lesend (Übersicht) | Interaktiv per AJAX editierbar |
| Audit-Log | Standard-Logging (`Log::info`) | Integration `owen-it/laravel-auditing` |

---

*Konzept-Ende. Implementierung nach Absprache gemäß Phasenplan.*

