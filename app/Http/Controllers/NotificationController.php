<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Hole alle Benachrichtigungen des Benutzers
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Hole ungelesene Benachrichtigungen (für Dropdown)
     */
    public function unread()
    {
        $notifications = auth()->user()
            ->unreadNotifications()
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Markiere eine Benachrichtigung als gelesen
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        // Redirect zur URL in der Benachrichtigung falls vorhanden
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }

        return back()->with('success', 'Benachrichtigung als gelesen markiert.');
    }

    /**
     * Markiere alle Benachrichtigungen als gelesen
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Alle Benachrichtigungen wurden als gelesen markiert.');
    }

    /**
     * Lösche eine Benachrichtigung
     */
    public function destroy($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'Benachrichtigung wurde gelöscht.');
    }

    /**
     * Lösche alle gelesenen Benachrichtigungen
     */
    public function deleteRead()
    {
        auth()->user()
            ->readNotifications()
            ->delete();

        return back()->with('success', 'Alle gelesenen Benachrichtigungen wurden gelöscht.');
    }
}
