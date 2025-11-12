<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\CriticalLogNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogNotificationService
{
    /**
     * Kritische Log-Levels die Benachrichtigungen auslösen
     */
    protected array $criticalLevels = ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY'];

    /**
     * Zeitfenster für Duplicate-Detection (in Minuten)
     */
    protected int $duplicateWindow = 5;

    /**
     * Maximale Anzahl ähnlicher Fehler vor Gruppierung
     */
    protected int $groupThreshold = 5;

    /**
     * Prüfe ob ein Log-Eintrag Benachrichtigungen auslösen sollte
     */
    public function shouldNotify(object $logEntry): bool
    {
        // Prüfe ob Level kritisch ist
        if (!in_array($logEntry->level_name, $this->criticalLevels)) {
            return false;
        }

        // Prüfe ob bereits vor kurzem benachrichtigt wurde (Duplicate Detection)
        $cacheKey = $this->getDuplicateCacheKey($logEntry);
        if (Cache::has($cacheKey)) {
            // Erhöhe Counter für gruppierten Bericht
            $this->incrementDuplicateCounter($logEntry);
            return false;
        }

        // Markiere als benachrichtigt
        Cache::put($cacheKey, true, now()->addMinutes($this->duplicateWindow));

        return true;
    }

    /**
     * Sende Benachrichtigungen an berechtigte Benutzer
     */
    public function notifyUsers(object $logEntry): void
    {
        $users = $this->getUsersWithPermission();

        if ($users->isEmpty()) {
            Log::warning('Keine Benutzer mit Permission "receive-critical-log-notifications" gefunden');
            return;
        }

        // Prüfe ob es gruppierte Fehler gibt
        $duplicateCount = $this->getDuplicateCount($logEntry);

        foreach ($users as $user) {
            try {
                $user->notify(new CriticalLogNotification($logEntry, $duplicateCount));
            } catch (\Exception $e) {
                Log::error('Fehler beim Senden der Log-Benachrichtigung', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Critical log notification sent', [
            'log_id' => $logEntry->id,
            'level' => $logEntry->level_name,
            'notified_users' => $users->count(),
            'duplicate_count' => $duplicateCount,
        ]);
    }

    /**
     * Hole alle Benutzer mit der entsprechenden Permission
     */
    public function getUsersWithPermission()
    {
        return User::permission('receive-critical-log-notifications')->get();
    }

    /**
     * Erstelle Cache-Key für Duplicate-Detection
     */
    protected function getDuplicateCacheKey(object $logEntry): string
    {
        // Verwende Message-Hash für ähnliche Fehler
        $messageHash = md5($logEntry->message);
        return "log_notification:{$logEntry->level_name}:{$messageHash}";
    }

    /**
     * Erhöhe Duplicate-Counter
     */
    protected function incrementDuplicateCounter(object $logEntry): void
    {
        $counterKey = $this->getDuplicateCacheKey($logEntry) . ':count';
        Cache::increment($counterKey);
        Cache::put($counterKey, Cache::get($counterKey, 1), now()->addMinutes($this->duplicateWindow));
    }

    /**
     * Hole Anzahl der duplizierten Fehler
     */
    protected function getDuplicateCount(object $logEntry): int
    {
        $counterKey = $this->getDuplicateCacheKey($logEntry) . ':count';
        return Cache::get($counterKey, 1);
    }

    /**
     * Sende täglichen Zusammenfassungs-Report
     */
    public function sendDailySummary(): void
    {
        $users = $this->getUsersWithPermission();

        if ($users->isEmpty()) {
            return;
        }

        $summary = $this->getDailySummary();

        if ($summary['total_errors'] === 0) {
            return; // Keine Fehler, kein Report
        }

        // TODO: Implementiere SummaryNotification wenn gewünscht
        Log::info('Daily log summary', $summary);
    }

    /**
     * Hole tägliche Zusammenfassung
     */
    protected function getDailySummary(): array
    {
        $today = now()->startOfDay();

        return [
            'total_errors' => DB::table(config('logtodb.collection', 'log'))
                ->whereIn('level_name', $this->criticalLevels)
                ->where('datetime', '>=', $today)
                ->count(),
            'by_level' => DB::table(config('logtodb.collection', 'log'))
                ->select('level_name', DB::raw('count(*) as count'))
                ->whereIn('level_name', $this->criticalLevels)
                ->where('datetime', '>=', $today)
                ->groupBy('level_name')
                ->get()
                ->pluck('count', 'level_name')
                ->toArray(),
        ];
    }
}

