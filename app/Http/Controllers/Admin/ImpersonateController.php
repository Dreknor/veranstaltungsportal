<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin'])->except('leave');
    }

    public function impersonate(User $user)
    {
        // Prevent self-impersonation
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Sie können sich nicht selbst verkörpern.');
        }

        // Prevent impersonating other admins
        if ($user->hasRole('admin')) {
            return back()->with('error', 'Sie können keine anderen Administratoren verkörpern.');
        }

        // Store original user ID in session
        session(['impersonator' => auth()->id()]);

        // Log the impersonation
        AuditLog::log(
            'impersonate_start',
            $user,
            null,
            null,
            "Admin " . auth()->user()->name . " hat Benutzer {$user->name} verkörpert"
        );

        // Login as the target user
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', "Sie verkörpern jetzt {$user->name}. Klicken Sie auf 'Verkörperung beenden' um zurückzukehren.");
    }

    public function leave()
    {
        if (!session()->has('impersonator')) {
            return redirect()->route('dashboard')
                ->with('error', 'Sie verkörpern gerade keinen Benutzer.');
        }

        $impersonatedUser = auth()->user();
        $originalUserId = session('impersonator');
        $originalUser = User::find($originalUserId);

        if (!$originalUser) {
            session()->forget('impersonator');
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Ursprünglicher Benutzer nicht gefunden.');
        }

        // Log the end of impersonation
        AuditLog::log(
            'impersonate_end',
            $impersonatedUser,
            null,
            null,
            "Admin {$originalUser->name} hat die Verkörperung von {$impersonatedUser->name} beendet"
        );

        // Clear impersonation session
        session()->forget('impersonator');

        // Login back as original user
        Auth::login($originalUser);

        return redirect()->route('admin.users.index')
            ->with('success', 'Verkörperung wurde beendet.');
    }
}

