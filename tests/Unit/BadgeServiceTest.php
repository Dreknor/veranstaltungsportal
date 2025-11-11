<?php

use App\Models\Badge;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Services\BadgeService;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->badgeService = app(BadgeService::class);
});

test('badge service can award badge to user', function () {
    Notification::fake();

    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    $result = $this->badgeService->awardBadge($user, $badge);

    expect($result)->toBeTrue()
        ->and($user->hasBadge($badge->id))->toBeTrue();
});

test('badge service does not award badge twice', function () {
    Notification::fake();

    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    $this->badgeService->awardBadge($user, $badge);
    $result = $this->badgeService->awardBadge($user, $badge);

    expect($result)->toBeFalse()
        ->and($user->badges()->count())->toBe(1);
});

test('badge service calculates user stats correctly', function () {
    $user = User::factory()->create();

    Badge::factory()->count(10)->create();
    $earnedBadges = Badge::factory()->count(3)->create(['points' => 10]);

    foreach ($earnedBadges as $badge) {
        $user->awardBadge($badge);
    }

    $stats = $this->badgeService->getUserBadgeStats($user);

    expect($stats['total_badges'])->toBe(13)
        ->and($stats['earned_badges'])->toBe(3)
        ->and($stats['total_points'])->toBe(30)
        ->and($stats['completion_percentage'])->toBeGreaterThan(0);
});

test('badge service checks bookings count requirement', function () {
    Notification::fake();

    $user = User::factory()->create();
    $event = Event::factory()->create();
    $badge = Badge::factory()->create([
        'requirements' => ['bookings_count' => 3],
    ]);

    // Create 2 paid bookings - should not earn badge yet
    Booking::factory()->count(2)->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => 'paid',
    ]);

    $newBadges = $this->badgeService->checkAndAwardBadges($user);
    expect($newBadges)->toBeEmpty();

    // Create 3rd paid booking - should earn badge
    Booking::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'payment_status' => 'paid',
    ]);

    $newBadges = $this->badgeService->checkAndAwardBadges($user);
    expect($newBadges)->toHaveCount(1)
        ->and($user->hasBadge($badge->id))->toBeTrue();
});

test('badge service checks events attended requirement', function () {
    Notification::fake();

    $user = User::factory()->create();
    $event = Event::factory()->create();
    $badge = Badge::factory()->create([
        'requirements' => ['events_attended' => 2],
    ]);

    // Create 1 checked-in booking - should not earn badge yet
    Booking::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'checked_in' => true,
    ]);

    $newBadges = $this->badgeService->checkAndAwardBadges($user);
    expect($newBadges)->toBeEmpty();

    // Create 2nd checked-in booking - should earn badge
    $event2 = Event::factory()->create();
    Booking::factory()->create([
        'user_id' => $user->id,
        'event_id' => $event2->id,
        'checked_in' => true,
    ]);

    $newBadges = $this->badgeService->checkAndAwardBadges($user);
    expect($newBadges)->toHaveCount(1)
        ->and($user->hasBadge($badge->id))->toBeTrue();
});

test('badge service calculates badge progress', function () {
    $user = User::factory()->create();
    $event = Event::factory()->create();
    $badge = Badge::factory()->create([
        'requirements' => ['events_attended' => 5],
    ]);

    // Create 2 checked-in bookings
    Booking::factory()->count(2)->create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'checked_in' => true,
    ]);

    $progress = $this->badgeService->getBadgeProgress($user, $badge);

    expect($progress)->toHaveKey('events_attended')
        ->and($progress['events_attended']['current'])->toBe(2)
        ->and($progress['events_attended']['required'])->toBe(5)
        ->and($progress['events_attended']['percentage'])->toBe(40)
        ->and($progress['events_attended']['completed'])->toBeFalse();
});

test('badge service generates leaderboard', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    $badge1 = Badge::factory()->create(['points' => 100]);
    $badge2 = Badge::factory()->create(['points' => 50]);

    $user1->awardBadge($badge1);
    $user1->awardBadge($badge2); // Total: 150

    $user2->awardBadge($badge1); // Total: 100

    $leaderboard = $this->badgeService->getLeaderboard(10);

    expect($leaderboard)->toHaveCount(2)
        ->and($leaderboard[0]['user']->id)->toBe($user1->id)
        ->and($leaderboard[0]['total_points'])->toBe(150)
        ->and($leaderboard[1]['user']->id)->toBe($user2->id)
        ->and($leaderboard[1]['total_points'])->toBe(100);
});

test('badge service seeds default badges', function () {
    $this->badgeService->seedDefaultBadges();

    expect(Badge::count())->toBeGreaterThan(0);

    // Check for specific badges
    expect(Badge::where('slug', 'first-steps')->exists())->toBeTrue()
        ->and(Badge::where('slug', 'hauptfach-mensch')->exists())->toBeTrue()
        ->and(Badge::where('slug', 'lifelong-learner')->exists())->toBeTrue();
});

test('badge service stats include badges by type', function () {
    $user = User::factory()->create();

    $attendanceBadge = Badge::factory()->attendance()->create();
    $achievementBadge = Badge::factory()->achievement()->create();
    $specialBadge = Badge::factory()->special()->create();

    $user->awardBadge($attendanceBadge);
    $user->awardBadge($achievementBadge);

    $stats = $this->badgeService->getUserBadgeStats($user);

    expect($stats['badges_by_type']['attendance'])->toBe(1)
        ->and($stats['badges_by_type']['achievement'])->toBe(1)
        ->and($stats['badges_by_type']['special'])->toBe(0);
});

