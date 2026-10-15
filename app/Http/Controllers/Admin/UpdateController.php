<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class UpdateController extends Controller
{
    public function __construct(private UpdateService $updater) {}

    public function index(): View
    {
        $status = null;
        $error = null;

        // Während eines Updates nicht parallel fetchen
        if (! $this->updater->isRunning()) {
            try {
                $status = $this->updater->status();
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        }

        return view('admin.updates.index', [
            'status' => $status,
            'error' => $error,
            'state' => $this->updater->state(),
            'log' => $this->updater->log(),
            'enabled' => (bool) config('updater.enabled'),
        ]);
    }

    public function store(): RedirectResponse
    {
        abort_unless(config('updater.enabled'), 403, 'Der Online-Updater ist deaktiviert.');

        if ($this->updater->isRunning()) {
            return back()->with('error', 'Es läuft bereits ein Update.');
        }

        $this->updater->startInBackground();

        Log::info('Online-Update gestartet', ['user_id' => auth()->id()]);

        return redirect()->route('admin.updates.index')
            ->with('success', 'Das Update wurde gestartet.');
    }

    public function progress(): JsonResponse
    {
        return response()->json([
            'state' => $this->updater->state(),
            'running' => $this->updater->isRunning(),
            'log' => $this->updater->log(),
        ]);
    }
}
