<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\Request;

class LegalPageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Übersicht aller Rechtseiten
     */
    public function index()
    {
        $pages = LegalPage::orderBy('type')->get()->keyBy('type');

        return view('admin.legal-pages.index', compact('pages'));
    }

    /**
     * Bearbeitungsformular für eine Rechtseite
     */
    public function edit(string $type)
    {
        abort_unless(array_key_exists($type, LegalPage::TYPES), 404);

        $page = LegalPage::firstOrCreate(
            ['type' => $type],
            [
                'title'   => LegalPage::TYPES[$type],
                'content' => '',
            ]
        );

        return view('admin.legal-pages.edit', compact('page'));
    }

    /**
     * Rechtseite speichern
     */
    public function update(Request $request, string $type)
    {
        abort_unless(array_key_exists($type, LegalPage::TYPES), 404);

        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $page = LegalPage::firstOrCreate(
            ['type' => $type],
            ['title' => LegalPage::TYPES[$type], 'content' => '']
        );

        $page->update([
            'title'           => $validated['title'],
            'content'         => $validated['content'],
            'last_updated_at' => now(),
            'updated_by'      => auth()->id(),
        ]);

        return redirect()
            ->route('admin.legal-pages.index')
            ->with('success', '"' . $page->title . '" wurde erfolgreich gespeichert.');
    }
}

