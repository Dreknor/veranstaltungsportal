<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\User;
use App\Notifications\BadgeEarnedNotification;
use Illuminate\Support\Facades\DB;

class BadgeService
{
    /**
     * Check and award all applicable badges to a user
     */
    public function checkAndAwardBadges(User $user): array
    {
        $newlyEarnedBadges = [];
        $allBadges = Badge::active()->get();

        foreach ($allBadges as $badge) {
            if (!$user->hasBadge($badge->id) && $this->meetsRequirements($user, $badge)) {
                $user->awardBadge($badge);
                $newlyEarnedBadges[] = $badge;

                // Send notification
                $user->notify(new BadgeEarnedNotification($badge));
            }
        }

        return $newlyEarnedBadges;
    }

    /**
     * Check if user meets badge requirements
     */
    protected function meetsRequirements(User $user, Badge $badge): bool
    {
        return $user->meetsRequirements($badge);
    }

    /**
     * Get user's badge statistics
     */
    public function getUserBadgeStats(User $user): array
    {
        $totalBadges = Badge::active()->count();
        $earnedBadges = $user->badges()->count();
        $totalPoints = $user->getTotalBadgePoints();

        $badgesByType = [
            'attendance' => $user->badges()->where('type', 'attendance')->count(),
            'achievement' => $user->badges()->where('type', 'achievement')->count(),
            'special' => $user->badges()->where('type', 'special')->count(),
        ];

        return [
            'total_badges' => $totalBadges,
            'earned_badges' => $earnedBadges,
            'completion_percentage' => $totalBadges > 0 ? round(($earnedBadges / $totalBadges) * 100, 1) : 0,
            'total_points' => $totalPoints,
            'badges_by_type' => $badgesByType,
            'recent_badges' => $user->badges()->limit(5)->get(),
        ];
    }

    /**
     * Get leaderboard based on badge points
     */
    public function getLeaderboard(int $limit = 10): array
    {
        return User::select('users.*')
            ->selectSub(function ($query) {
                $query->select(DB::raw('COALESCE(SUM(badges.points), 0)'))
                    ->from('user_badges')
                    ->join('badges', 'user_badges.badge_id', '=', 'badges.id')
                    ->whereColumn('user_badges.user_id', 'users.id');
            }, 'total_points')
            ->selectSub(function ($query) {
                $query->selectRaw('COUNT(*)')
                    ->from('user_badges')
                    ->whereColumn('user_badges.user_id', 'users.id');
            }, 'badges_count')
            ->where(function($query) {
                // Only include users with public profiles OR who allow networking
                $query->where('show_profile_public', true)
                      ->orWhere('allow_networking', true);
            })
            ->whereRaw('(select COALESCE(SUM(badges.points), 0) from user_badges inner join badges on user_badges.badge_id = badges.id where user_badges.user_id = users.id) > 0') // Only users with at least one badge
            ->orderByDesc('total_points')
            ->limit($limit)
            ->get()
            ->map(function ($user) {
                return [
                    'user' => $user,
                    'total_points' => $user->total_points,
                    'badges_count' => $user->badges_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get badge progress for a user
     */
    public function getBadgeProgress(User $user, Badge $badge): array
    {
        $requirements = $badge->requirements ?? [];
        $progress = [];

        foreach ($requirements as $key => $targetValue) {
            $currentValue = $this->getCurrentValue($user, $key);
            $progress[$key] = [
                'current' => $currentValue,
                'required' => $targetValue,
                'percentage' => min(100, round(($currentValue / $targetValue) * 100)),
                'completed' => $currentValue >= $targetValue,
            ];
        }

        return $progress;
    }

    /**
     * Get current value for a requirement key
     */
    protected function getCurrentValue(User $user, string $key): int
    {
        switch ($key) {
            case 'bookings_count':
                return $user->bookings()->where('payment_status', 'paid')->count();

            case 'events_attended':
                return $user->bookings()->where('checked_in', true)->count();

            case 'events_organized':
                return $user->events()->count();

            case 'reviews_written':
                return \App\Models\EventReview::where('user_id', $user->id)->count();

            case 'total_hours_attended':
                return (int) $user->bookings()
                    ->where('checked_in', true)
                    ->join('events', 'bookings.event_id', '=', 'events.id')
                    ->sum('events.duration');

            case 'categories_explored':
                return $user->bookings()
                    ->join('events', 'bookings.event_id', '=', 'events.id')
                    ->distinct('events.event_category_id')
                    ->count('events.event_category_id');

            case 'early_bird_bookings':
                return $user->bookings()
                    ->join('events', 'bookings.event_id', '=', 'events.id')
                    ->whereRaw('bookings.created_at < DATE_SUB(events.start_date, INTERVAL 7 DAY)')
                    ->count();

            default:
                return 0;
        }
    }

    /**
     * Award a specific badge to a user
     */
    public function awardBadge(User $user, Badge $badge): bool
    {
        if ($user->hasBadge($badge->id)) {
            return false;
        }

        $user->awardBadge($badge);
        $user->notify(new BadgeEarnedNotification($badge));

        return true;
    }

    /**
     * Create predefined badges for the education platform
     */
    public function seedDefaultBadges(): void
    {
        $badges = [
            [
                'name' => 'Erste Schritte',
                'slug' => 'first-steps',
                'description' => 'Erste Fortbildung erfolgreich besucht',
                'type' => 'achievement',
                'color' => '#10B981',
                'points' => 10,
                'requirements' => ['events_attended' => 1],
            ],
            [
                'name' => 'Wissbegierig',
                'slug' => 'curious-learner',
                'description' => '5 Fortbildungen besucht',
                'type' => 'achievement',
                'color' => '#3B82F6',
                'points' => 50,
                'requirements' => ['events_attended' => 5],
            ],
            [
                'name' => 'Bildungsexperte',
                'slug' => 'education-expert',
                'description' => '10 Fortbildungen besucht',
                'type' => 'achievement',
                'color' => '#8B5CF6',
                'points' => 100,
                'requirements' => ['events_attended' => 10],
            ],
            [
                'name' => 'Lebenslanges Lernen',
                'slug' => 'lifelong-learner',
                'description' => '25 Fortbildungen besucht',
                'type' => 'achievement',
                'color' => '#F59E0B',
                'points' => 250,
                'requirements' => ['events_attended' => 25],
            ],
            [
                'name' => 'Vielseitig',
                'slug' => 'versatile',
                'description' => 'Fortbildungen in 5 verschiedenen Kategorien besucht',
                'type' => 'achievement',
                'color' => '#EC4899',
                'points' => 75,
                'requirements' => ['categories_explored' => 5],
            ],
            [
                'name' => 'Frühbucher',
                'slug' => 'early-bird',
                'description' => '10 Fortbildungen mindestens 7 Tage im Voraus gebucht',
                'type' => 'achievement',
                'color' => '#14B8A6',
                'points' => 50,
                'requirements' => ['early_bird_bookings' => 10],
            ],
            [
                'name' => 'Zeitinvestition',
                'slug' => 'time-investment',
                'description' => '50 Stunden Fortbildung absolviert',
                'type' => 'attendance',
                'color' => '#06B6D4',
                'points' => 100,
                'requirements' => ['total_hours_attended' => 50],
            ],
            [
                'name' => 'Bildungsmarathon',
                'slug' => 'education-marathon',
                'description' => '100 Stunden Fortbildung absolviert',
                'type' => 'attendance',
                'color' => '#F97316',
                'points' => 200,
                'requirements' => ['total_hours_attended' => 100],
            ],
            [
                'name' => 'Feedback-Geber',
                'slug' => 'feedback-provider',
                'description' => '5 Bewertungen geschrieben',
                'type' => 'achievement',
                'color' => '#84CC16',
                'points' => 25,
                'requirements' => ['reviews_written' => 5],
            ],
            [
                'name' => 'Veranstalter',
                'slug' => 'organizer',
                'description' => 'Erste eigene Fortbildung organisiert',
                'type' => 'special',
                'color' => '#EF4444',
                'points' => 100,
                'requirements' => ['events_organized' => 1],
            ],
            [
                'name' => 'Hauptfach Mensch',
                'slug' => 'hauptfach-mensch',
                'description' => 'Teilnahme an der Aktion "Hauptfach Mensch"',
                'type' => 'special',
                'color' => '#6366F1',
                'points' => 150,
                'requirements' => [], // Manually awarded
            ],
        ];

        foreach ($badges as $badgeData) {
            Badge::updateOrCreate(
                ['slug' => $badgeData['slug']],
                $badgeData
            );
        }
    }
}

