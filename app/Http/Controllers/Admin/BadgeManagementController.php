<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BadgeManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'admin']);
    }

    /**
     * Display all badges
     */
    public function index(Request $request)
    {
        $query = Badge::query();

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        $badges = $query->orderBy('points', 'desc')->paginate(20);

        // Statistics
        $stats = [
            'total' => Badge::count(),
            'attendance' => Badge::where('type', 'attendance')->count(),
            'achievement' => Badge::where('type', 'achievement')->count(),
            'special' => Badge::where('type', 'special')->count(),
            'total_awarded' => DB::table('user_badges')->count(),
        ];

        return view('admin.badges.index', compact('badges', 'stats'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.badges.create');
    }

    /**
     * Store a new badge
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:badges,name',
            'description' => 'required|string',
            'type' => 'required|in:attendance,achievement,special',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'points' => 'required|integer|min:0|max:1000',
            'requirement_type' => 'required|in:events_attended,hours_attended,total_hours_attended,bookings_made,reviews_written,connections_made,events_organized,revenue_generated,participants_reached,categories_explored,early_bird_bookings,event_categories',
            'requirement_value' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Build requirements array
        $validated['requirements'] = [
            $validated['requirement_type'] => (int) $validated['requirement_value']
        ];

        // Remove individual requirement fields as they're now in the array
        unset($validated['requirement_type'], $validated['requirement_value']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = $validated['slug'] . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/badges'), $filename);
            $validated['image_path'] = 'images/badges/' . $filename;
        }

        Badge::create($validated);

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge erfolgreich erstellt.');
    }

    /**
     * Show edit form
     */
    public function edit(Badge $badge)
    {
        return view('admin.badges.edit', compact('badge'));
    }

    /**
     * Update a badge
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:badges,name,' . $badge->id,
            'description' => 'required|string',
            'type' => 'required|in:attendance,achievement,special',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'points' => 'required|integer|min:0|max:1000',
            'requirement_type' => 'required|in:events_attended,hours_attended,total_hours_attended,bookings_made,reviews_written,connections_made,events_organized,revenue_generated,participants_reached,categories_explored,early_bird_bookings,event_categories',
            'requirement_value' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        // Update slug if name changed
        if ($validated['name'] !== $badge->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Build requirements array
        $validated['requirements'] = [
            $validated['requirement_type'] => (int) $validated['requirement_value']
        ];

        // Remove individual requirement fields as they're now in the array
        unset($validated['requirement_type'], $validated['requirement_value']);

        // Handle image removal
        if ($request->input('remove_image')) {
            if ($badge->image_path && file_exists(public_path($badge->image_path))) {
                unlink(public_path($badge->image_path));
            }
            $validated['image_path'] = null;
        }
        // Handle new image upload
        elseif ($request->hasFile('image')) {
            // Delete old image if exists
            if ($badge->image_path && file_exists(public_path($badge->image_path))) {
                unlink(public_path($badge->image_path));
            }

            $image = $request->file('image');
            $filename = $validated['slug'] . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/badges'), $filename);
            $validated['image_path'] = 'images/badges/' . $filename;
        }

        $badge->update($validated);

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge erfolgreich aktualisiert.');
    }

    /**
     * Delete a badge
     */
    public function destroy(Badge $badge)
    {
        // Count users with this badge
        $userCount = $badge->users()->count();

        if ($userCount > 0) {
            return back()->with('error', "Dieser Badge wurde bereits an {$userCount} Benutzer vergeben und kann nicht gelöscht werden.");
        }

        $badge->delete();

        return redirect()
            ->route('admin.badges.index')
            ->with('success', 'Badge erfolgreich gelöscht.');
    }

    /**
     * Show badge details with awarded users
     */
    public function show(Badge $badge)
    {
        $badge->load(['users' => function ($query) {
            $query->orderBy('user_badges.earned_at', 'desc');
        }]);

        return view('admin.badges.show', compact('badge'));
    }

    /**
     * Award badge to specific user
     */
    public function award(Request $request, Badge $badge)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        if ($user->badges()->where('badge_id', $badge->id)->exists()) {
            return back()->with('error', 'Dieser Benutzer hat den Badge bereits erhalten.');
        }

        $user->badges()->attach($badge->id, [
            'earned_at' => now(),
        ]);

        // Send notification
        $user->notify(new \App\Notifications\BadgeEarnedNotification($badge));

        return back()->with('success', "Badge '{$badge->name}' wurde an {$user->name} vergeben.");
    }

    /**
     * Revoke badge from specific user
     */
    public function revoke(Request $request, Badge $badge)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);

        if (!$user->badges()->where('badge_id', $badge->id)->exists()) {
            return back()->with('error', 'Dieser Benutzer hat diesen Badge nicht.');
        }

        $user->badges()->detach($badge->id);

        return back()->with('success', "Badge '{$badge->name}' wurde von {$user->name} entfernt.");
    }
}

