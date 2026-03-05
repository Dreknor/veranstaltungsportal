<?php

namespace App\Observers;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;

/**
 * Invalidiert den ATOM-Feed-Cache, wenn eine Veranstaltung gespeichert oder gelöscht wird.
 */
class EventFeedObserver
{
    public function saved(Event $event): void
    {
        // Nur öffentlich relevante Änderungen invalidieren
        if ($event->is_published || $event->wasChanged('is_published')) {
            $this->clearFeedCache();
        }
    }

    public function deleted(Event $event): void
    {
        $this->clearFeedCache();
    }

    public function restored(Event $event): void
    {
        $this->clearFeedCache();
    }

    private function clearFeedCache(): void
    {
        $supportsTagging = in_array(config('cache.default'), ['redis', 'memcached']);

        if ($supportsTagging) {
            // Mit Redis/Memcached: alle Feed-Einträge via Tag löschen
            Cache::tags(['atom_feed'])->flush();
        } else {
            // Ohne Tag-Unterstützung: kurze TTL (5 min) reicht für die meisten Fälle.
            // Den Haupt-Feed-Key explizit löschen.
            Cache::forget('atom_feed_' . md5(serialize([]) . serialize([]) . 'feed/atom.xml'));
        }
    }
}

