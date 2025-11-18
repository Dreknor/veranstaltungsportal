<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventManagementController extends Controller
{

    public function index(Request $request)
    {
        $query = Event::with(['organization', 'category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            } elseif ($request->status === 'featured') {
                $query->where('is_featured', true);
            }
        }

        if ($request->filled('category')) {
            $query->where('event_category_id', $request->category);
        }

        $events = $query->withCount('bookings')->latest()->paginate(20);
        $categories = EventCategory::where('is_active', true)->get();

        return view('admin.events.index', compact('events', 'categories'));
    }

    public function togglePublish(Event $event)
    {
        $event->update([
            'is_published' => !$event->is_published
        ]);

        return back()->with('success', 'Event-Status erfolgreich geändert.');
    }

    public function toggleFeatured(Event $event)
    {
        $event->update([
            'is_featured' => !$event->is_featured
        ]);

        return back()->with('success', 'Featured-Status erfolgreich geändert.');
    }

    public function destroy(Event $event)
    {
        // Check if event has bookings
        if ($event->bookings()->count() > 0) {
            return back()->with('error', 'Event kann nicht gelöscht werden, da bereits Buchungen vorhanden sind.');
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event erfolgreich gelöscht.');
    }
}

