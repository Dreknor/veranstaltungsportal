<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DataPrivacyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('data-privacy.index');
    }

    /**
     * Export all user data (DSGVO Art. 15)
     */
    public function exportData()
    {
        $user = auth()->user();

        // Collect all user data
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'bio' => $user->bio,
                'user_type' => $user->user_type,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'bookings' => $user->bookings()->with(['event', 'items'])->get()->toArray(),
            'organized_events' => $user->events()->get()->toArray(),
            'favorites' => $user->favoriteEvents()->get()->toArray(),
            'badges' => $user->badges()->get()->toArray(),
            'connections' => [
                'following' => $user->following()->get()->toArray(),
                'followers' => $user->followers()->get()->toArray(),
            ],
            'notifications' => $user->notifications()->get()->toArray(),
            'audit_logs' => AuditLog::where('user_id', $user->id)->get()->toArray(),
        ];

        // Log the export
        AuditLog::log(
            'data_export',
            $user,
            null,
            null,
            "Benutzer hat seine Daten exportiert"
        );

        $filename = "user_data_{$user->id}_" . now()->format('Y-m-d_His') . '.json';

        return response()->json($data)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }

    /**
     * Request account deletion (DSGVO Art. 17)
     */
    public function requestDeletion(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        $upcomingEvents = $user->events()
            ->where('start_date', '>', now())
            ->count();

        if ($upcomingEvents > 0) {
            return back()->with('error',
                "Sie können Ihr Konto nicht löschen, solange Sie {$upcomingEvents} bevorstehende Veranstaltung(en) haben. Bitte löschen oder übertragen Sie diese zuerst."
            );
        }

        // Check if user has upcoming bookings
        $upcomingBookings = $user->bookings()
            ->whereHas('event', function ($query) {
                $query->where('start_date', '>', now());
            })
            ->count();

        if ($upcomingBookings > 0) {
            return back()->with('error',
                "Sie können Ihr Konto nicht löschen, solange Sie {$upcomingBookings} bevorstehende Buchung(en) haben. Bitte stornieren Sie diese zuerst."
            );
        }

        $userId = (int) $user->id;

        if (!$userId) {
            throw new \Exception("User ID is null or zero when trying to log account deletion");
        }

        // Log the deletion request BEFORE delete and logout
        $auditLog = AuditLog::create([
            'user_id' => $userId,
            'action' => 'account_deletion_requested',
            'auditable_type' => get_class($user),
            'auditable_id' => $user->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => "Benutzer hat Kontolöschung beantragt. Grund: " . ($request->reason ?? 'Nicht angegeben'),
        ]);

        // Double check the audit log was saved with correct user_id
        if ($auditLog->user_id !== $userId) {
            \Log::error("Audit log user_id mismatch", [
                'expected' => $userId,
                'actual' => $auditLog->user_id,
                'audit_log_id' => $auditLog->id
            ]);
        }

        // Mark account for deletion (soft delete)
        $user->delete();

        // Logout
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Ihr Konto wurde zur Löschung markiert. Die Daten werden innerhalb von 30 Tagen vollständig gelöscht.');
    }

    /**
     * Download all personal files (invoices, certificates, etc.)
     */
    public function downloadFiles()
    {
        $user = auth()->user();

        // Create temporary zip file
        $zipFileName = "user_files_{$user->id}_" . now()->format('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
            $hasFiles = false;

            // Add profile photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                $zip->addFile(
                    Storage::disk('public')->path($user->profile_photo),
                    'profile_photo/' . basename($user->profile_photo)
                );
                $hasFiles = true;
            }

            // Add booking certificates
            foreach ($user->bookings as $booking) {
                if ($booking->certificate_path && Storage::exists($booking->certificate_path)) {
                    $zip->addFile(
                        Storage::path($booking->certificate_path),
                        'certificates/' . basename($booking->certificate_path)
                    );
                    $hasFiles = true;
                }
            }

            // Add a readme if no files were added
            if (!$hasFiles) {
                $zip->addFromString('README.txt', 'Keine persönlichen Dateien gefunden.');
            }

            $zip->close();

            // Log the download
            AuditLog::log(
                'files_download',
                $user,
                null,
                null,
                "Benutzer hat alle persönlichen Dateien heruntergeladen"
            );

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Fehler beim Erstellen der ZIP-Datei.');
    }

    /**
     * Show privacy settings
     */
    public function settings()
    {
        $user = auth()->user();
        return view('data-privacy.settings', compact('user'));
    }

    /**
     * Update privacy settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'allow_networking' => 'boolean',
            'show_profile_public' => 'boolean',
            'allow_data_analytics' => 'boolean',
        ]);

        $user = auth()->user();

        $oldValues = [
            'allow_networking' => $user->allow_networking ?? false,
            'show_profile_public' => $user->show_profile_public ?? false,
            'allow_data_analytics' => $user->allow_data_analytics ?? false,
        ];

        $user->update([
            'allow_networking' => $request->boolean('allow_networking'),
            'show_profile_public' => $request->boolean('show_profile_public'),
            'allow_data_analytics' => $request->boolean('allow_data_analytics'),
        ]);

        // Log the update
        AuditLog::log(
            'privacy_settings_updated',
            $user,
            $oldValues,
            [
                'allow_networking' => $user->allow_networking,
                'show_profile_public' => $user->show_profile_public,
                'allow_data_analytics' => $user->allow_data_analytics,
            ],
            "Benutzer hat Datenschutzeinstellungen aktualisiert"
        );

        return back()->with('success', 'Datenschutzeinstellungen wurden erfolgreich aktualisiert.');
    }
}

