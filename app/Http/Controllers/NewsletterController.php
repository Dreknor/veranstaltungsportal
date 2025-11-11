<?php

namespace App\Http\Controllers;

use App\Models\EventCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsletterController extends Controller
{
    /**
     * Show interests and newsletter settings
     */
    public function edit()
    {
        $categories = EventCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        $recommendedEvents = Auth::user()->getRecommendedEvents(6);

        return view('settings.interests', compact('categories', 'recommendedEvents'));
    }

    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Toggle newsletter subscription
            if ($user->newsletter_subscribed) {
                $user->unsubscribeFromNewsletter();
                return redirect()->back()->with('success', 'Sie haben den Newsletter erfolgreich abbestellt.');
            } else {
                $user->subscribeToNewsletter();
                return redirect()->back()->with('success', 'Sie haben den Newsletter erfolgreich abonniert!');
            }
        }

        // For non-authenticated users, store email in a separate newsletter_subscribers table
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ]);

        \DB::table('newsletter_subscribers')->insert([
            'email' => $request->email,
            'subscribed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Sie haben den Newsletter erfolgreich abonniert!');
    }

    /**
     * Unsubscribe from newsletter
     */
    public function unsubscribe(Request $request)
    {
        if (Auth::check()) {
            Auth::user()->unsubscribeFromNewsletter();

            return redirect()->back()->with('success', 'Sie haben den Newsletter abbestellt.');
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        \DB::table('newsletter_subscribers')->where('email', $request->email)->delete();

        return redirect()->back()->with('success', 'Sie haben den Newsletter abbestellt.');
    }

    /**
     * Update user interests
     */
    public function updateInterests(Request $request)
    {
        $request->validate([
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:event_categories,id'
        ]);

        $user = Auth::user();
        $user->interested_category_ids = $request->category_ids ?? [];
        $user->save();

        return redirect()->back()->with('success', 'Ihre Interessen wurden aktualisiert.');
    }
}

