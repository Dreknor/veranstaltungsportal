<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Services\BadgeService;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->middleware('auth');
        $this->badgeService = $badgeService;
    }

    /**
     * Display user's badges
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get all active badges
        $allBadges = Badge::active()->get();

        // Separate earned and unearned badges
        $earnedBadges = $user->badges;
        $earnedBadgeIds = $earnedBadges->pluck('id')->toArray();

        $unearnedBadges = $allBadges->filter(function ($badge) use ($earnedBadgeIds) {
            return !in_array($badge->id, $earnedBadgeIds);
        });

        // Get badge statistics
        $stats = $this->badgeService->getUserBadgeStats($user);

        // Get badge progress for unearned badges
        $badgeProgress = [];
        foreach ($unearnedBadges as $badge) {
            $badgeProgress[$badge->id] = $this->badgeService->getBadgeProgress($user, $badge);
        }

        return view('badges.index', compact('earnedBadges', 'unearnedBadges', 'stats', 'badgeProgress'));
    }

    /**
     * Show badge details
     */
    public function show(Badge $badge)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $hasEarned = $user->hasBadge($badge->id);

        $progress = null;
        if (!$hasEarned) {
            $progress = $this->badgeService->getBadgeProgress($user, $badge);
        }

        $earnedByCount = $badge->users()->count();

        return view('badges.show', compact('badge', 'hasEarned', 'progress', 'earnedByCount'));
    }

    /**
     * Toggle badge highlight status
     */
    public function toggleHighlight(Request $request, Badge $badge)
    {
        $user = $request->user();

        if (!$user->hasBadge($badge->id)) {
            return response()->json(['error' => 'Badge not earned'], 403);
        }

        $user->toggleBadgeHighlight($badge->id);

        return response()->json(['success' => true]);
    }

    /**
     * Get leaderboard
     */
    public function leaderboard()
    {
        $leaderboard = $this->badgeService->getLeaderboard(50);
        $userRank = null;

        // Find current user's rank
        if (auth()->check()) {
            $userId = auth()->id();
            foreach ($leaderboard as $index => $entry) {
                if ($entry['user']->id === $userId) {
                    $userRank = $index + 1;
                    break;
                }
            }

            // If user not in top 50, calculate their rank
            if (!$userRank) {
                $userPoints = auth()->user()->getTotalBadgePoints();
                $userRank = \App\Models\User::select('users.*')
                    ->selectSub(function ($query) {
                        $query->select(\Illuminate\Support\Facades\DB::raw('COALESCE(SUM(badges.points), 0)'))
                            ->from('user_badges')
                            ->join('badges', 'user_badges.badge_id', '=', 'badges.id')
                            ->whereColumn('user_badges.user_id', 'users.id');
                    }, 'total_points')
                    ->having('total_points', '>', $userPoints)
                    ->count() + 1;
            }
        }

        return view('badges.leaderboard', compact('leaderboard', 'userRank'));
    }
}

