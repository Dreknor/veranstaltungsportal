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
                'is_organizer' => $user->is_organizer,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
            'bookings' => $user->bookings()->with(['event', 'items'])->get()->toArray(),
            'organized_events' => $user->organizedEvents()->with(['ticketTypes', 'discountCodes'])->get()->toArray(),
            'reviews' => $user->reviews()->with('event')->get()->toArray(),
            'favorites' => $user->favorites()->get()->toArray(),
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

        // Check if user has upcoming events as organizer
        $upcomingEvents = $user->organizedEvents()
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

        // Log the deletion request
        AuditLog::log(
            'account_deletion_requested',
            $user,
            null,
            null,
            "Benutzer hat Kontolöschung beantragt. Grund: " . ($request->reason ?? 'Nicht angegeben')
        );

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

        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            // Add profile photo if exists
            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                $zip->addFile(
                    Storage::disk('public')->path($user->profile_photo),
                    'profile_photo/' . basename($user->profile_photo)
                );
            }

            // Add booking certificates
            foreach ($user->bookings as $booking) {
                if ($booking->certificate_path && Storage::exists($booking->certificate_path)) {
                    $zip->addFile(
                        Storage::path($booking->certificate_path),
                        'certificates/' . basename($booking->certificate_path)
                    );
                }
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
     * Redirect to privacy settings (now consolidated)
     */
    public function settings()
    {
        return redirect()->route('settings.privacy.edit')
            ->with('info', 'Datenschutzeinstellungen werden jetzt zentral in den Profil-Einstellungen verwaltet.');
    }

    /**
     * Redirect to privacy settings update (now consolidated)
     */
    public function updateSettings(Request $request)
    {
        return redirect()->route('settings.privacy.edit')
            ->with('info', 'Bitte nutzen Sie die zentrale Datenschutz-Einstellungsseite.');
    }
}

