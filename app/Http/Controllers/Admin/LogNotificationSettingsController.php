<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LogNotificationService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class LogNotificationSettingsController extends Controller
{
    protected LogNotificationService $notificationService;

    public function __construct(LogNotificationService $notificationService)
    {
        $this->middleware(['auth', 'admin']);
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $permission = Permission::where('name', 'receive-critical-log-notifications')->first();

        $usersWithPermission = $this->notificationService->getUsersWithPermission();
        $allAdmins = User::role('admin')->get();

        return view('admin.system-logs.notification-settings', compact(
            'permission',
            'usersWithPermission',
            'allAdmins'
        ));
    }

    public function givePermission(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->givePermissionTo('receive-critical-log-notifications');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', "Benutzer {$user->name} erhält jetzt Benachrichtigungen bei kritischen Fehlern.");
    }

    public function revokePermission(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);

        // Direkte Permission entziehen
        if ($user->hasDirectPermission('receive-critical-log-notifications')) {
            $user->revokePermissionTo('receive-critical-log-notifications');
        }

        // Falls der User die Permission noch über die Admin-Rolle hat,
        // muss die Permission von der Rolle entfernt werden.
        // Damit andere Admins sie behalten, bekommen diese die Permission direkt zugewiesen.
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && $adminRole->hasPermissionTo('receive-critical-log-notifications')) {
            // Allen anderen Admins die Permission direkt geben (die sie nicht schon direkt haben)
            User::role('admin')
                ->where('id', '!=', $user->id)
                ->get()
                ->each(function ($admin) {
                    if (!$admin->hasDirectPermission('receive-critical-log-notifications')) {
                        $admin->givePermissionTo('receive-critical-log-notifications');
                    }
                });

            // Permission von der Rolle entfernen
            $adminRole->revokePermissionTo('receive-critical-log-notifications');

            // Spatie Permission Cache leeren
            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }

        // Cache auch nach direktem Entzug leeren
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', "Benachrichtigungen für {$user->name} wurden deaktiviert.");
    }

    public function testNotification(Request $request)
    {
        $user = auth()->user();

        // Erstelle Test-Log-Eintrag
        $testLog = (object) [
            'id' => 0,
            'level_name' => 'CRITICAL',
            'channel' => 'test',
            'message' => 'Dies ist eine Test-Benachrichtigung für kritische System-Logs',
            'datetime' => now()->format('Y-m-d H:i:s'),
            'context' => null,
        ];

        try {
            $user->notify(new \App\Notifications\CriticalLogNotification($testLog, 1));
            return back()->with('success', 'Test-Benachrichtigung wurde gesendet! Bitte überprüfen Sie Ihre E-Mails.');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Senden der Test-Benachrichtigung: ' . $e->getMessage());
        }
    }
}

