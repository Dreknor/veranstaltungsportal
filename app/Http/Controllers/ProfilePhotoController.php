<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProfilePhotoController extends Controller
{
    /**
     * Serve user profile photo with authentication
     */
    public function show(Request $request, User $user): BinaryFileResponse
    {
        // User muss eingeloggt sein
        if (!auth()->check()) {
            abort(403, 'Sie müssen angemeldet sein, um Profilbilder anzusehen.');
        }

        // Kein Profilbild vorhanden
        if (!$user->profile_photo) {
            abort(404, 'Kein Profilbild vorhanden.');
        }

        // Prüfe ob die Datei existiert
        if (!Storage::disk('local')->exists($user->profile_photo)) {
            abort(404, 'Profilbild nicht gefunden.');
        }

        // Prüfe Zugriffsrechte:
        // 1. Der Nutzer kann sein eigenes Bild sehen
        // 2. Bei öffentlichen Profilen kann jeder das Bild sehen
        // 3. Bei Networking-erlaubten Profilen können verbundene Nutzer das Bild sehen
        /** @var User $viewer */
        $viewer = auth()->user();

        $canView = $viewer->id === $user->id
            || $user->show_profile_public
            || ($user->allow_networking && $viewer->isFollowing($user));

        if (!$canView) {
            abort(403, 'Sie haben keine Berechtigung, dieses Profilbild anzusehen.');
        }

        // Liefere das Bild aus
        $path = Storage::disk('local')->path($user->profile_photo);
        $mimeType = Storage::disk('local')->mimeType($user->profile_photo);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}

