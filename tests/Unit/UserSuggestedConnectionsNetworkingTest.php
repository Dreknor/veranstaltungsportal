<?php

use App\Models\User;
use App\Models\UserConnection;

it('suggests only users with allow_networking = true when interests match', function () {
    $user = User::factory()->create([
        'interested_category_ids' => [1,2],
        'allow_networking' => true,
    ]);

    $allowed = User::factory()->count(3)->create([
        'interested_category_ids' => [2,3],
        'allow_networking' => true,
    ]);

    $blocked = User::factory()->count(2)->create([
        'interested_category_ids' => [1,4],
        'allow_networking' => false,
    ]);

    $suggestions = $user->getSuggestedConnections(10);

    // Alle vorgeschlagenen Nutzer müssen allow_networking=true haben
    expect($suggestions->every(fn($u) => $u->allow_networking))->toBeTrue();

    // Sicherstellen dass keiner der blockierten IDs erscheint
    foreach ($blocked as $b) {
        expect($suggestions->pluck('id'))->not()->toContain($b->id);
    }

    // Mindestens einer der erlaubten Kandidaten vorhanden
    expect($suggestions->pluck('id')->intersect($allowed->pluck('id'))->count())->toBeGreaterThan(0);
});

it('excludes already connected users from suggestions', function () {
    $user = User::factory()->create([
        'interested_category_ids' => [1,2],
        'allow_networking' => true,
    ]);

    $candidate = User::factory()->create([
        'interested_category_ids' => [2,5],
        'allow_networking' => true,
    ]);

    // Create accepted connection (mutual)
    UserConnection::create([
        'follower_id' => $user->id,
        'following_id' => $candidate->id,
        'status' => 'accepted',
    ]);
    UserConnection::create([
        'follower_id' => $candidate->id,
        'following_id' => $user->id,
        'status' => 'accepted',
    ]);

    $suggestions = $user->getSuggestedConnections(10);

    expect($suggestions->pluck('id'))->not()->toContain($candidate->id);
});

it('falls back to active users with allow_networking when no interests present', function () {
    $user = User::factory()->create([
        'interested_category_ids' => [],
        'allow_networking' => true,
    ]);

    $allowed = User::factory()->count(2)->create([
        'allow_networking' => true,
    ]);

    $notAllowed = User::factory()->count(2)->create([
        'allow_networking' => false,
    ]);

    $suggestions = $user->getSuggestedConnections(10);

    expect($suggestions->count())->toBeGreaterThan(0);
    expect($suggestions->every(fn($u) => $u->allow_networking))->toBeTrue();
    foreach ($notAllowed as $u) {
        expect($suggestions->pluck('id'))->not()->toContain($u->id);
    }
});

it('does not include users with empty interested_category_ids in interest-based suggestions', function () {
    $user = User::factory()->create([
        'interested_category_ids' => [1,2],
        'allow_networking' => true,
    ]);

    User::factory()->create([
        'interested_category_ids' => [],
        'allow_networking' => true,
    ]);

    $matching = User::factory()->create([
        'interested_category_ids' => [2,3],
        'allow_networking' => true,
    ]);

    $suggestions = $user->getSuggestedConnections(10);

    expect($suggestions->pluck('id'))->toContain($matching->id);
    expect($suggestions->count())->toBe(1); // nur der mit tatsächlichem Overlap
});
