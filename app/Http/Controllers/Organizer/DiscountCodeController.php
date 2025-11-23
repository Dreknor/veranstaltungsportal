<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\DiscountCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscountCodeController extends Controller
{

    public function index(Event $event)
    {
        $this->authorize('update', $event);

        $discountCodes = $event->discountCodes()
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('organizer.discount-codes.index', compact('event', 'discountCodes'));
    }

    public function create(Event $event)
    {
        $this->authorize('update', $event);

        return view('organizer.discount-codes.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code',
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $value > 100) {
                        $fail('Der Rabatt in Prozent darf nicht größer als 100 sein.');
                    }
                },
            ],
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        // Convert code to uppercase
        $validated['code'] = strtoupper($validated['code']);
        $validated['event_id'] = $event->id;

        DiscountCode::create($validated);

        return redirect()
            ->route('organizer.events.discount-codes.index', $event)
            ->with('success', 'Rabattcode erfolgreich erstellt!');
    }

    public function edit(Event $event, DiscountCode $discountCode)
    {
        $this->authorize('update', $event);

        if ($discountCode->event_id !== $event->id) {
            abort(404);
        }

        return view('organizer.discount-codes.edit', compact('event', 'discountCode'));
    }

    public function update(Request $request, Event $event, DiscountCode $discountCode)
    {
        $this->authorize('update', $event);

        if ($discountCode->event_id !== $event->id) {
            abort(404);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:discount_codes,code,' . $discountCode->id,
            'type' => 'required|in:percentage,fixed',
            'value' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->type === 'percentage' && $value > 100) {
                        $fail('Der Rabatt in Prozent darf nicht größer als 100 sein.');
                    }
                },
            ],
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        // Convert code to uppercase
        $validated['code'] = strtoupper($validated['code']);

        $discountCode->update($validated);

        return redirect()
            ->route('organizer.events.discount-codes.index', $event)
            ->with('success', 'Rabattcode erfolgreich aktualisiert!');
    }

    public function destroy(Event $event, DiscountCode $discountCode)
    {
        $this->authorize('update', $event);

        if ($discountCode->event_id !== $event->id) {
            abort(404);
        }

        $discountCode->delete();

        return redirect()
            ->route('organizer.events.discount-codes.index', $event)
            ->with('success', 'Rabattcode erfolgreich gelöscht!');
    }

    public function toggle(Event $event, DiscountCode $discountCode)
    {
        $this->authorize('update', $event);

        if ($discountCode->event_id !== $event->id) {
            abort(404);
        }

        $discountCode->update([
            'is_active' => !$discountCode->is_active
        ]);

        return back()->with('success', 'Rabattcode-Status erfolgreich geändert!');
    }

    public function generate(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $code = strtoupper(Str::random(8));

        return response()->json(['code' => $code]);
    }
}

