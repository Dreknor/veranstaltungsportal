<?php

namespace App\Observers;

use App\Services\LogNotificationService;
use Illuminate\Support\Facades\Log;

class SystemLogObserver
{
    protected LogNotificationService $notificationService;

    public function __construct(LogNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the "created" event.
     */
    public function created($log): void
    {
        // Prüfe ob Benachrichtigung nötig ist
        if ($this->notificationService->shouldNotify($log)) {
            // Sende Benachrichtigung asynchron (via Queue)
            dispatch(function () use ($log) {
                $this->notificationService->notifyUsers($log);
            })->afterResponse();
        }
    }
}
