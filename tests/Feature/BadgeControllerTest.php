<?php

use App\Models\Badge;
use App\Models\User;

test('guest cannot access badges page', function () {
    $response = $this->get(route('badges.index'));

    $response->assertRedirect(route('login'));
});

test('authenticated user can view badges page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('badges.index'));

    $response->assertStatus(200)
        ->assertViewIs('badges.index')
        ->assertViewHas(['earnedBadges', 'unearnedBadges', 'stats', 'badgeProgress']);
});

test('badges page shows earned and unearned badges correctly', function () {
    $user = User::factory()->create();
    $earnedBadge = Badge::factory()->create(['name' => 'Earned Badge']);
    $unearnedBadge = Badge::factory()->create(['name' => 'Unearned Badge']);

    $user->awardBadge($earnedBadge);

    $response = $this->actingAs($user)->get(route('badges.index'));

    $response->assertStatus(200)
        ->assertSee('Earned Badge')
        ->assertSee('Unearned Badge');
});

test('user can view badge details', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create([
        'name' => 'Test Badge',
        'description' => 'Test Description',
    ]);

    $response = $this->actingAs($user)->get(route('badges.show', $badge));

    $response->assertStatus(200)
        ->assertViewIs('badges.show')
        ->assertSee('Test Badge')
        ->assertSee('Test Description');
});

test('user can toggle badge highlight', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    $user->awardBadge($badge);

    $response = $this->actingAs($user)
        ->postJson(route('badges.toggle-highlight', $badge));

    $response->assertStatus(200)
        ->assertJson(['success' => true]);

    $userBadge = $user->badges()->first();
    expect($userBadge->pivot->is_highlighted)->toBeTrue();
});

test('user cannot highlight badge they havent earned', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('badges.toggle-highlight', $badge));

    $response->assertStatus(403)
        ->assertJson(['error' => 'Badge not earned']);
});

test('leaderboard page shows top users', function () {
    // Create users with different badge points
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $user3 = User::factory()->create();

    $badge1 = Badge::factory()->create(['points' => 100]);
    $badge2 = Badge::factory()->create(['points' => 50]);
    $badge3 = Badge::factory()->create(['points' => 25]);

    $user1->awardBadge($badge1);
    $user1->awardBadge($badge2); // Total: 150

    $user2->awardBadge($badge1); // Total: 100

    $user3->awardBadge($badge3); // Total: 25

    $response = $this->actingAs($user1)->get(route('badges.leaderboard'));

    $response->assertStatus(200)
        ->assertViewIs('badges.leaderboard')
        ->assertViewHas('leaderboard');
});

test('badge statistics are calculated correctly', function () {
    $user = User::factory()->create();

    Badge::factory()->count(10)->create();
    $earnedBadges = Badge::factory()->count(3)->create();

    foreach ($earnedBadges as $badge) {
        $user->awardBadge($badge);
    }

    $response = $this->actingAs($user)->get(route('badges.index'));

    $stats = $response->viewData('stats');

    expect($stats['earned_badges'])->toBe(3)
        ->and($stats['total_badges'])->toBe(13);
});

test('inactive badges are not shown', function () {
    $user = User::factory()->create();

    $activeBadge = Badge::factory()->create(['name' => 'Active Badge', 'is_active' => true]);
    $inactiveBadge = Badge::factory()->create(['name' => 'Inactive Badge', 'is_active' => false]);

    $response = $this->actingAs($user)->get(route('badges.index'));

    $response->assertStatus(200)
        ->assertSee('Active Badge')
        ->assertDontSee('Inactive Badge');
});

test('badge progress is shown for unearned badges', function () {
    $user = User::factory()->create();
    $badge = Badge::factory()->create([
        'requirements' => ['events_attended' => 5],
    ]);

    $response = $this->actingAs($user)->get(route('badges.index'));

    $badgeProgress = $response->viewData('badgeProgress');

    expect($badgeProgress)->toHaveKey($badge->id)
        ->and($badgeProgress[$badge->id])->toHaveKey('events_attended');
});

