<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('dashboard'));
    }

    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return back()->with('success', 'Alle Benachrichtigungen als gelesen markiert.');
    }

    public function destroy($id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'Benachrichtigung gelöscht.');
    }
}

