<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHasOrganizationContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user has verified email
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Check if user is organizer or has organizations
        if (!$user->isOrganizer() && $user->activeOrganizations()->count() === 0) {
            abort(403, 'Sie haben keine Berechtigung für den Organisator-Bereich. Bitte erstellen Sie zuerst eine Organisation.');
        }

        // Ensure user has a current organization selected
        $currentOrganization = $user->currentOrganization();

        if (!$currentOrganization) {
            // If user has organizations but none selected, redirect to organization selection
            if ($user->activeOrganizations()->count() > 0) {
                return redirect()->route('organizer.organizations.select')
                    ->with('warning', 'Bitte wählen Sie eine Organisation aus.');
            }

            // If user has no organizations, redirect to create one
            return redirect()->route('organizer.organizations.create')
                ->with('info', 'Bitte erstellen Sie zuerst eine Organisation.');
        }

        // Share current organization with all views
        view()->share('currentOrganization', $currentOrganization);

        return $next($request);
    }
}

