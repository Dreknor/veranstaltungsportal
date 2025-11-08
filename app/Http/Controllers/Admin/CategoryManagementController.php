<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $query = EventCategory::withCount('events');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $categories = $query->orderBy('name')->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_categories,name',
            'slug' => 'nullable|string|max:255|unique:event_categories,slug',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        EventCategory::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich erstellt.');
    }

    public function edit(EventCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, EventCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:event_categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:event_categories,slug,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:7',
            'is_active' => 'boolean',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich aktualisiert.');
    }

    public function destroy(EventCategory $category)
    {
        // Check if category has events
        if ($category->events()->count() > 0) {
            return back()->with('error', 'Kategorie kann nicht gelöscht werden, da sie noch Veranstaltungen enthält.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategorie erfolgreich gelöscht.');
    }

    public function toggleActive(EventCategory $category)
    {
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'aktiviert' : 'deaktiviert';

        return back()->with('success', "Kategorie erfolgreich {$status}.");
    }
}

