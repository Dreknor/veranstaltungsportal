<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HelpController extends Controller
{
    /**
     * Display the help center overview.
     */
    public function index(): View
    {
        $userType = $this->getUserType();

        return view('help.index', [
            'userType' => $userType,
        ]);
    }

    /**
     * Display a specific help article.
     */
    public function show(string $category, string $article): View
    {
        $userType = $this->getUserType();

        // Check if user has access to this category
        if (!$this->canAccessCategory($category, $userType)) {
            abort(403, 'Sie haben keine Berechtigung, diese Hilfe-Kategorie anzuzeigen.');
        }

        $viewPath = "help.{$category}.{$article}";

        if (!view()->exists($viewPath)) {
            abort(404, 'Hilfe-Artikel nicht gefunden.');
        }

        return view($viewPath, [
            'userType' => $userType,
            'category' => $category,
            'article' => $article,
        ]);
    }

    /**
     * Display help category overview.
     */
    public function category(string $category): View
    {
        $userType = $this->getUserType();

        // Check if user has access to this category
        if (!$this->canAccessCategory($category, $userType)) {
            abort(403, 'Sie haben keine Berechtigung, diese Hilfe-Kategorie anzuzeigen.');
        }

        $viewPath = "help.{$category}.index";

        if (!view()->exists($viewPath)) {
            abort(404, 'Hilfe-Kategorie nicht gefunden.');
        }

        return view($viewPath, [
            'userType' => $userType,
            'category' => $category,
        ]);
    }

    /**
     * Search help articles.
     */
    public function search(Request $request): View
    {
        $query = $request->input('q', '');
        $userType = $this->getUserType();

        $results = $this->searchArticles($query, $userType);

        return view('help.search', [
            'query' => $query,
            'results' => $results,
            'userType' => $userType,
        ]);
    }

    /**
     * Get the current user type.
     */
    private function getUserType(): string
    {
        if (!Auth::check()) {
            return 'guest';
        }

        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return 'admin';
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('organizer')) {
            return 'organizer';
        }

        if (property_exists($user, 'is_organizer') && $user->is_organizer) {
            return 'organizer';
        }

        return 'user';
    }

    /**
     * Check if user can access a specific category.
     */
    private function canAccessCategory(string $category, string $userType): bool
    {
        $categoryPermissions = [
            'user' => ['guest', 'user', 'organizer', 'admin'],
            'organizer' => ['organizer', 'admin'],
            'admin' => ['admin'],
        ];

        return isset($categoryPermissions[$category])
            && in_array($userType, $categoryPermissions[$category]);
    }

    /**
     * Search for help articles.
     */
    private function searchArticles(string $query, string $userType): array
    {
        if (empty($query)) {
            return [];
        }

        $query = strtolower($query);
        $results = [];

        // Define searchable articles based on user type
        $articles = $this->getSearchableArticles($userType);

        foreach ($articles as $article) {
            $score = 0;

            // Check title
            if (str_contains(strtolower($article['title']), $query)) {
                $score += 10;
            }

            // Check keywords
            foreach ($article['keywords'] as $keyword) {
                if (str_contains(strtolower($keyword), $query)) {
                    $score += 5;
                }
            }

            // Check description
            if (str_contains(strtolower($article['description']), $query)) {
                $score += 3;
            }

            if ($score > 0) {
                $article['score'] = $score;
                $results[] = $article;
            }
        }

        // Sort by relevance
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return $results;
    }

    /**
     * Get searchable articles based on user type.
     */
    private function getSearchableArticles(string $userType): array
    {
        $articles = [
            // User articles
            [
                'category' => 'user',
                'slug' => 'getting-started',
                'title' => 'Erste Schritte',
                'description' => 'Lernen Sie die Grundlagen der Plattform kennen',
                'keywords' => ['start', 'anfang', 'einführung', 'erste schritte', 'anmeldung'],
            ],
            [
                'category' => 'user',
                'slug' => 'finding-events',
                'title' => 'Veranstaltungen finden',
                'description' => 'So finden Sie passende Fortbildungen',
                'keywords' => ['suchen', 'filter', 'kategorien', 'veranstaltungen', 'events'],
            ],
            [
                'category' => 'user',
                'slug' => 'booking-events',
                'title' => 'Veranstaltungen buchen',
                'description' => 'Schritt-für-Schritt Anleitung zur Buchung',
                'keywords' => ['buchen', 'tickets', 'zahlung', 'buchung', 'anmeldung'],
            ],
            [
                'category' => 'user',
                'slug' => 'manage-bookings',
                'title' => 'Buchungen verwalten',
                'description' => 'Ihre Buchungen ansehen und verwalten',
                'keywords' => ['buchungen', 'stornieren', 'tickets', 'zertifikate', 'verwalten'],
            ],
            [
                'category' => 'user',
                'slug' => 'profile-settings',
                'title' => 'Profil & Einstellungen',
                'description' => 'Ihr Profil anpassen und Einstellungen ändern',
                'keywords' => ['profil', 'einstellungen', 'passwort', 'foto', 'daten'],
            ],
            [
                'category' => 'user',
                'slug' => 'notifications',
                'title' => 'Benachrichtigungen',
                'description' => 'Benachrichtigungen verwalten und einstellen',
                'keywords' => ['benachrichtigungen', 'mitteilungen', 'email', 'erinnerungen'],
            ],
            [
                'category' => 'user',
                'slug' => 'social-features',
                'title' => 'Netzwerk & Kontakte',
                'description' => 'Mit anderen Nutzern vernetzen',
                'keywords' => ['kontakte', 'netzwerk', 'verbindungen', 'vernetzung', 'sozial'],
            ],
            [
                'category' => 'user',
                'slug' => 'badges',
                'title' => 'Badges & Erfolge',
                'description' => 'Badges sammeln und Ihren Fortschritt verfolgen',
                'keywords' => ['badges', 'auszeichnungen', 'erfolge', 'punkte', 'leaderboard'],
            ],
            [
                'category' => 'user',
                'slug' => 'favorites',
                'title' => 'Favoriten & Merkliste',
                'description' => 'Veranstaltungen als Favoriten speichern',
                'keywords' => ['favoriten', 'merkliste', 'gespeichert', 'wishlist'],
            ],
            [
                'category' => 'user',
                'slug' => 'reviews',
                'title' => 'Bewertungen schreiben',
                'description' => 'Veranstaltungen bewerten und rezensieren',
                'keywords' => ['bewertungen', 'reviews', 'feedback', 'sterne', 'rezensionen'],
            ],
            [
                'category' => 'user',
                'slug' => 'privacy',
                'title' => 'Datenschutz & Privatsphäre',
                'description' => 'Ihre Daten und Privatsphäre schützen',
                'keywords' => ['datenschutz', 'dsgvo', 'privatsphäre', 'daten', 'export'],
            ],
            [
                'category' => 'user',
                'slug' => 'troubleshooting',
                'title' => 'Häufige Probleme',
                'description' => 'Lösungen für häufige Probleme',
                'keywords' => ['probleme', 'fehler', 'hilfe', 'support', 'faq'],
            ],
        ];

        // Filter based on user type
        if ($userType === 'guest') {
            return array_filter($articles, fn($a) => $a['category'] === 'user');
        }

        return $articles;
    }
}

