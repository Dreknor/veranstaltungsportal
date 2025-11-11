<?php

use App\Models\Badge;
use App\Models\User;

test('badge can be created', function () {
    $badge = Badge::create([
        'name' => 'Test Badge',
        'slug' => 'test-badge',
        'description' => 'A test badge',
        'type' => 'achievement',
        'color' => '#3B82F6',
        'points' => 10,
        'requirements' => ['bookings_count' => 1],
    ]);

    expect($badge)->toBeInstanceOf(Badge::class)
        ->and($badge->slug)->toBe('test-badge')
        ->and($badge->name)->toBe('Test Badge');

    $this->assertDatabaseHas('badges', [
        'slug' => 'test-badge',
        'name' => 'Test Badge',
    ]);
});

test('badge can be awarded to user', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    $badge->awardTo($user);

    expect($user->hasBadge($badge->id))->toBeTrue()
        ->and($badge->isEarnedBy($user))->toBeTrue();
});

test('badge cannot be awarded twice', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    $badge->awardTo($user);
    $badge->awardTo($user); // Try to award again

    // Should only have one entry
    expect($user->badges()->count())->toBe(1);
});

test('user can have multiple badges', function () {
    $user = User::factory()->create();
    $badge1 = Badge::factory()->create();
    $badge2 = Badge::factory()->create();

    $user->awardBadge($badge1);
    $user->awardBadge($badge2);

    expect($user->badges()->count())->toBe(2)
        ->and($user->hasBadge($badge1->id))->toBeTrue()
        ->and($user->hasBadge($badge2->id))->toBeTrue();
});

test('total badge points calculated correctly', function () {
    $user = User::factory()->create();
    $badge1 = Badge::factory()->create(['points' => 10]);
    $badge2 = Badge::factory()->create(['points' => 25]);
    $badge3 = Badge::factory()->create(['points' => 50]);

    $user->awardBadge($badge1);
    $user->awardBadge($badge2);
    $user->awardBadge($badge3);

    expect($user->getTotalBadgePoints())->toBe(85);
});

test('badge highlight can be toggled', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    $user->awardBadge($badge);
    $userBadge = $user->badges()->first();

    expect($userBadge->pivot->is_highlighted)->toBeFalse();

    $user->toggleBadgeHighlight($badge->id);
    $userBadge = $user->badges()->first();

    expect($userBadge->pivot->is_highlighted)->toBeTrue();

    $user->toggleBadgeHighlight($badge->id);
    $userBadge = $user->badges()->first();

    expect($userBadge->pivot->is_highlighted)->toBeFalse();
});

test('badge scope active filters correctly', function () {
    Badge::factory()->create(['is_active' => true]);
    Badge::factory()->create(['is_active' => true]);
    Badge::factory()->create(['is_active' => false]);

    $activeBadges = Badge::active()->get();

    expect($activeBadges)->toHaveCount(2);
});

test('badge scope of type filters correctly', function () {
    Badge::factory()->create(['type' => 'achievement']);
    Badge::factory()->create(['type' => 'achievement']);
    Badge::factory()->create(['type' => 'attendance']);
    Badge::factory()->create(['type' => 'special']);

    $achievementBadges = Badge::ofType('achievement')->get();
    $attendanceBadges = Badge::ofType('attendance')->get();

    expect($achievementBadges)->toHaveCount(2)
        ->and($attendanceBadges)->toHaveCount(1);
});

test('badge can check user by slug', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create(['slug' => 'first-steps']);

    $user->awardBadge($badge);

    expect($user->hasBadge('first-steps'))->toBeTrue()
        ->and($user->hasBadge($badge->id))->toBeTrue();
});

test('highlighted badges relationship works', function () {
    $user = User::factory()->create();
    $badge1 = Badge::factory()->create();
    $badge2 = Badge::factory()->create();
    $badge3 = Badge::factory()->create();

    $user->awardBadge($badge1);
    $user->awardBadge($badge2);
    $user->awardBadge($badge3);

    $user->toggleBadgeHighlight($badge1->id);
    $user->toggleBadgeHighlight($badge3->id);

    $highlightedBadges = $user->highlightedBadges()->get();

    expect($highlightedBadges)->toHaveCount(2)
        ->and($highlightedBadges->contains($badge1))->toBeTrue()
        ->and($highlightedBadges->contains($badge3))->toBeTrue()
        ->and($highlightedBadges->contains($badge2))->toBeFalse();
});

