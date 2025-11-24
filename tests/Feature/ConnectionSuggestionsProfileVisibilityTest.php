<?php

use App\Models\User;
use App\Models\UserConnection;
use function Pest\Laravel\actingAs;

it('shows profile button for public profile suggestions', function () {
    $viewer = User::factory()->create(['allow_networking' => true, 'interested_category_ids' => [1]]);
    $publicUser = User::factory()->create([
        'show_profile_public' => true,
        'allow_networking' => true,
        'interested_category_ids' => [1]
    ]);

    actingAs($viewer);
    $suggestions = $viewer->getSuggestedConnections(10);
    expect($suggestions->pluck('id'))->toContain($publicUser->id);
    expect($publicUser->canBeViewedBy($viewer))->toBeTrue();
});

it('shows profile button for connected non-public profiles if networking allowed', function () {
    $viewer = User::factory()->create(['allow_networking' => true, 'interested_category_ids' => [2]]);
    $nonPublic = User::factory()->create([
        'show_profile_public' => false,
        'allow_networking' => true,
        'interested_category_ids' => [2]
    ]);

    // Verbindung herstellen (accepted)
    UserConnection::create([
        'follower_id' => $viewer->id,
        'following_id' => $nonPublic->id,
        'status' => 'accepted'
    ]);
    UserConnection::create([
        'follower_id' => $nonPublic->id,
        'following_id' => $viewer->id,
        'status' => 'accepted'
    ]);

    expect($nonPublic->canBeViewedBy($viewer))->toBeTrue();
});

it('hides profile button for non-public, not-connected profiles', function () {
    $viewer = User::factory()->create(['allow_networking' => true, 'interested_category_ids' => [3]]);
    $hiddenUser = User::factory()->create([
        'show_profile_public' => false,
        'allow_networking' => true,
        'interested_category_ids' => [3]
    ]);

    expect($hiddenUser->canBeViewedBy($viewer))->toBeFalse();
});
