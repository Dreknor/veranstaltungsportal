<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class AtomFeedController extends Controller
{
    /**
     * Erlaubte SELECT-Felder – sensible Felder (access_code, online_url, etc.) werden
     * niemals selektiert, auch wenn sie im Request angegeben werden.
     */
    private const SAFE_FIELDS = [
        'id',
        'slug',
        'title',
        'description',
        'start_date',
        'end_date',
        'event_type',
        'venue_name',
        'venue_address',
        'venue_city',
        'venue_postal_code',
        'venue_country',
        'venue_latitude',
        'venue_longitude',
        'price_from',
        'is_featured',
        'featured_image',
        'organization_id',
        'event_category_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Haupt-Feed: alle öffentlichen anstehenden Veranstaltungen
     */
    public function index(Request $request): Response
    {
        return $this->buildFeed($request);
    }

    /**
     * Sub-Feed: Veranstaltungen einer bestimmten Kategorie
     */
    public function byCategory(Request $request, string $slug): Response
    {
        $category = EventCategory::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        return $this->buildFeed($request, [
            'category_id'       => $category?->id,
            'feed_title_suffix' => $category ? " – {$category->name}" : '',
        ]);
    }

    /**
     * Sub-Feed: Veranstaltungen in einer bestimmten Stadt
     */
    public function byCity(Request $request, string $city): Response
    {
        return $this->buildFeed($request, [
            'city'              => $city,
            'feed_title_suffix' => ' – ' . $city,
        ]);
    }

    /**
     * Sub-Feed: Veranstaltungen einer bestimmten Organisation
     */
    public function byOrganization(Request $request, string $slug): Response
    {
        $org = Organization::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        return $this->buildFeed($request, [
            'organization_id'   => $org?->id,
            'feed_title_suffix' => $org ? " von {$org->name}" : '',
        ]);
    }

    /**
     * Sub-Feed: Veranstaltungen nach Event-Typ (physical / online / hybrid)
     */
    public function byType(Request $request, string $type): Response
    {
        $labels = [
            'physical' => 'Präsenzveranstaltungen',
            'online'   => 'Online-Veranstaltungen',
            'hybrid'   => 'Hybride Veranstaltungen',
        ];

        return $this->buildFeed($request, [
            'event_type'        => $type,
            'feed_title_suffix' => ' – ' . ($labels[$type] ?? $type),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Interne Methoden
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Gemeinsame Feed-Generierungslogik für alle Endpunkte.
     *
     * @param  array<string, mixed>  $preFilters  Vorfilter aus Route-Parametern
     */
    private function buildFeed(Request $request, array $preFilters = []): Response
    {
        // 1. Query-Parameter validieren (bei Fehlern: leeres Array → leerer Feed)
        $validated = $this->validateParams($request);

        // 2. Eindeutigen Cache-Key aus Parametern + Pfad generieren
        $cacheKey = 'atom_feed_' . md5(
            serialize($validated) . serialize($preFilters) . $request->path()
        );

        // 3. Events aus Cache holen oder neu laden (TTL: 5 Minuten)
        /** @var \Illuminate\Support\Collection $events */
        $events = Cache::remember($cacheKey, 300, function () use ($validated, $preFilters) {
            return $this->queryEvents($validated, $preFilters);
        });

        // 4. Last-Modified-Datum berechnen
        $lastModified = $events->isEmpty()
            ? now()
            : Carbon::parse($events->max('updated_at'));

        // 5. ETag für Conditional-GET (If-None-Match)
        $etag = '"' . md5($cacheKey . $lastModified->timestamp) . '"';

        // 6. 304 Not Modified zurückgeben, wenn sich nichts geändert hat
        if ($request->header('If-None-Match') === $etag) {
            return response('', 304);
        }

        // 7. Feed-Daten für das Blade-Template zusammenstellen
        $feedData = [
            'events'       => $events,
            'feedUrl'      => $request->fullUrl(),
            'feedId'       => url($request->path()),
            'feedTitle'    => config('app.name') . ' – Veranstaltungen'
                              . ($preFilters['feed_title_suffix'] ?? ''),
            'lastModified' => $lastModified,
            'siteUrl'      => config('app.url'),
        ];

        // 8. XML rendern und Response mit Security-Headers zurückgeben
        $content = view('feed.atom', $feedData)->render();

        return response($content, 200)
            ->header('Content-Type', 'application/atom+xml; charset=utf-8')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Robots-Tag', 'noindex, nofollow')
            ->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=60')
            ->header('ETag', $etag)
            ->header('Last-Modified', $lastModified->toRfc7231String());
    }

    /**
     * Query-Parameter aus dem Request validieren.
     * Bei Validierungsfehlern wird ein leeres Array zurückgegeben
     * (→ kein Fehler, sondern leerer Feed).
     *
     * @return array<string, mixed>
     */
    private function validateParams(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'category'     => 'nullable|string|max:100|alpha_dash',
            // Nur Buchstaben, Leerzeichen, Bindestriche – max. 100 Zeichen, kein ReDoS-Risiko
            'city'         => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\-]{2,100}$/u'],
            'organization' => 'nullable|string|max:100|alpha_dash',
            'type'         => 'nullable|in:physical,online,hybrid',
            'featured'     => 'nullable|boolean',
            'date_from'    => 'nullable|date|date_format:Y-m-d',
            'date_to'      => 'nullable|date|date_format:Y-m-d|after:date_from',
            'limit'        => 'nullable|integer|min:1|max:100',
            // Mindestens 2 Zeichen, maximal 100.
            // \p{P} würde % und _ als Satzzeichen durchlassen – daher explizit ausgeschlossen.
            // Erlaubt: Unicode-Buchstaben, Ziffern, Leerzeichen, Bindestriche, Punkte, Kommas, Schrägstriche.
            'q'            => ['nullable', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\p{N}\s\-\.\,\/]+$/u'],
        ]);

        return $validator->fails() ? [] : $validator->validated();
    }

    /**
     * Escaped LIKE-Sonderzeichen im Suchbegriff, um Wildcard-Injection zu verhindern.
     * '%' und '_' sind SQL-LIKE-Wildcards und müssen escaped werden.
     */
    private function sanitizeSearchTerm(string $term): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term);
    }

    /**
     * Events aus der Datenbank abfragen – mit unveränderlichen Sicherheits-Filtern.
     *
     * @param  array<string, mixed>  $params      Validierte Query-Parameter
     * @param  array<string, mixed>  $preFilters  Vorfilter aus Route-Parametern
     * @return \Illuminate\Support\Collection
     */
    private function queryEvents(array $params, array $preFilters): \Illuminate\Support\Collection
    {
        $query = Event::query()
            ->select(self::SAFE_FIELDS)
            ->with([
                'category:id,name,slug',
                'organization:id,name,slug,website',
            ])
            // ── PFLICHT-Filter: niemals durch Parameter überschreibbar ────────
            ->where('is_published', true)
            ->where('is_private', false)
            ->where('is_cancelled', false)
            ->where('start_date', '>=', now())
            // ────────────────────────────────────────────────────────────────
            ->orderBy('start_date', 'asc')
            ->limit($params['limit'] ?? 50);

        // ── Vorfilter aus Route-Parametern (z. B. /feed/atom/city/berlin) ──
        if (!empty($preFilters['category_id'])) {
            $query->where('event_category_id', $preFilters['category_id']);
        }
        if (!empty($preFilters['organization_id'])) {
            $query->where('organization_id', $preFilters['organization_id']);
        }
        if (!empty($preFilters['city'])) {
            $city = $this->sanitizeSearchTerm($preFilters['city']);
            $query->where('venue_city', 'LIKE', "%{$city}%");
        }
        if (!empty($preFilters['event_type'])) {
            $query->where('event_type', $preFilters['event_type']);
        }

        // ── Optionale Query-Parameter ──────────────────────────────────────
        if (!empty($params['category'])) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $params['category']));
        }
        if (!empty($params['city'])) {
            $city = $this->sanitizeSearchTerm($params['city']);
            $query->where('venue_city', 'LIKE', "%{$city}%");
        }
        if (!empty($params['organization'])) {
            $query->whereHas('organization', fn ($q) => $q->where('slug', $params['organization']));
        }
        if (!empty($params['type'])) {
            $query->where('event_type', $params['type']);
        }
        if (!empty($params['featured'])) {
            $query->where('is_featured', true);
        }
        if (!empty($params['date_from'])) {
            $query->where('start_date', '>=', Carbon::parse($params['date_from'])->startOfDay());
        }
        if (!empty($params['date_to'])) {
            $query->where('start_date', '<=', Carbon::parse($params['date_to'])->endOfDay());
        }
        if (!empty($params['q'])) {
            $term = $this->sanitizeSearchTerm($params['q']);
            $query->where(fn ($s) =>
                $s->where('title', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%")
            );
        }

        return $query->get();
    }
}






