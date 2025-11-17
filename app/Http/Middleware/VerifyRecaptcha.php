<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifyRecaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $action = null): Response
    {
        // Skip if reCAPTCHA is disabled
        if (!config('recaptcha.enabled')) {
            return $next($request);
        }

        // Skip for authenticated users if configured
        if (config('recaptcha.skip_for_authenticated') && auth()->check()) {
            return $next($request);
        }

        // Skip for GET requests
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        // Get the reCAPTCHA token from the request
        $token = $request->input('g-recaptcha-response');

        if (!$token) {
            Log::warning('reCAPTCHA token missing', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            return back()->withErrors([
                'recaptcha' => 'Bitte bestätigen Sie, dass Sie kein Roboter sind.',
            ])->withInput();
        }

        // Verify the token with Google
        try {
            $response = Http::asForm()->post(config('recaptcha.verify_url'), [
                'secret' => config('recaptcha.secret_key'),
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();

            if (!$result['success']) {
                Log::warning('reCAPTCHA verification failed', [
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'errors' => $result['error-codes'] ?? [],
                ]);

                return back()->withErrors([
                    'recaptcha' => 'reCAPTCHA-Überprüfung fehlgeschlagen. Bitte versuchen Sie es erneut.',
                ])->withInput();
            }

            // Check the score
            $score = $result['score'] ?? 0;
            $threshold = $this->getThreshold($action);

            if ($score < $threshold) {
                Log::warning('reCAPTCHA score too low', [
                    'url' => $request->fullUrl(),
                    'ip' => $request->ip(),
                    'score' => $score,
                    'threshold' => $threshold,
                    'action' => $action ?? 'default',
                ]);

                return back()->withErrors([
                    'recaptcha' => 'Ihre Anfrage konnte nicht verifiziert werden. Bitte versuchen Sie es später erneut.',
                ])->withInput();
            }

            // Check if the action matches
            if ($action && isset($result['action']) && $result['action'] !== $action) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $action,
                    'received' => $result['action'],
                    'url' => $request->fullUrl(),
                ]);
            }

            Log::info('reCAPTCHA verification successful', [
                'url' => $request->fullUrl(),
                'score' => $score,
                'action' => $result['action'] ?? 'unknown',
            ]);

        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification exception', [
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            // On error, we'll let the request through but log it
            // You can change this behavior based on your security requirements
            if (config('app.env') === 'production') {
                return back()->withErrors([
                    'recaptcha' => 'Ein Fehler ist bei der Sicherheitsüberprüfung aufgetreten. Bitte versuchen Sie es erneut.',
                ])->withInput();
            }
        }

        return $next($request);
    }

    /**
     * Get the threshold for the given action.
     */
    protected function getThreshold(?string $action): float
    {
        if (!$action) {
            return config('recaptcha.score_threshold');
        }

        $actionConfig = config("recaptcha.actions.{$action}");

        if (!$actionConfig || !($actionConfig['enabled'] ?? true)) {
            return config('recaptcha.score_threshold');
        }

        return $actionConfig['threshold'] ?? config('recaptcha.score_threshold');
    }
}

