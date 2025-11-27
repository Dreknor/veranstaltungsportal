<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventDate;
use Illuminate\Http\Request;

/**
 * @deprecated This controller is deprecated.
 * Event series functionality has been moved to EventController with multiple dates support.
 * Events can now have multiple dates via the EventDate model.
 */
class SeriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return redirect()
            ->route('organizer.events.index')
            ->with('info', 'Die Veranstaltungsreihen-Funktion wurde in die Event-Verwaltung integriert. Events können jetzt mehrere Termine haben.');
    }

    public function create()
    {
        return redirect()
            ->route('organizer.events.create')
            ->with('info', 'Erstellen Sie ein Event und fügen Sie mehrere Termine hinzu.');
    }

    public function show($id)
    {
        // Try to redirect to event if this was a series
        return redirect()
            ->route('organizer.events.index')
            ->with('info', 'Veranstaltungsreihen werden nicht mehr unterstützt. Verwenden Sie Events mit mehreren Terminen.');
    }

    public function edit($id)
    {
        return redirect()
            ->route('organizer.events.index')
            ->with('info', 'Veranstaltungsreihen werden nicht mehr unterstützt. Verwenden Sie Events mit mehreren Terminen.');
    }

    public function store(Request $request)
    {
        return redirect()
            ->route('organizer.events.create')
            ->with('info', 'Erstellen Sie ein Event und fügen Sie mehrere Termine hinzu.');
    }

    public function update(Request $request, $id)
    {
        return redirect()
            ->route('organizer.events.index')
            ->with('info', 'Veranstaltungsreihen werden nicht mehr unterstützt.');
    }

    public function destroy($id)
    {
        return redirect()
            ->route('organizer.events.index')
            ->with('info', 'Veranstaltungsreihen werden nicht mehr unterstützt.');
    }
}

