<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsOrganizer
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

        $hasOrgMembership = method_exists($user, 'activeOrganizations') && $user->activeOrganizations()->exists();
        $hasOrganizerRole = method_exists($user, 'hasRole') && ($user->hasRole('organizer') || $user->hasRole('admin'));

        if (!($hasOrgMembership || $hasOrganizerRole)) {
            abort(403, 'Nur Organisatoren haben Zugriff auf diesen Bereich.');
        }

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
