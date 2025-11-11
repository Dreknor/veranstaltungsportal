<?php

use App\Models\User;
use App\Models\UserConnection;

test('user can follow another user', function () {
    $follower = User::factory()->create();
    $following = User::factory()->create();

    $connection = $follower->sendConnectionRequest($following);

    expect($connection)->toBeInstanceOf(UserConnection::class);
    expect($connection->status)->toBe('pending');
    expect($connection->follower_id)->toBe($follower->id);
    expect($connection->following_id)->toBe($following->id);
});

test('user can check if following another user', function () {
    $follower = User::factory()->create();
    $following = User::factory()->create();

    expect($follower->isFollowing($following))->toBeFalse();

    UserConnection::factory()->create([
        'follower_id' => $follower->id,
        'following_id' => $following->id,
        'status' => 'accepted',
    ]);

    expect($follower->fresh()->isFollowing($following))->toBeTrue();
});

test('user can check if followed by another user', function () {
    $follower = User::factory()->create();
    $following = User::factory()->create();

    expect($following->isFollowedBy($follower))->toBeFalse();

    UserConnection::factory()->create([
        'follower_id' => $follower->id,
        'following_id' => $following->id,
        'status' => 'accepted',
    ]);

    expect($following->fresh()->isFollowedBy($follower))->toBeTrue();
});

test('user can accept connection request', function () {
    $follower = User::factory()->create();
    $following = User::factory()->create();

    $connection = UserConnection::factory()->pending()->create([
        'follower_id' => $follower->id,
        'following_id' => $following->id,
    ]);

    expect($connection->isPending())->toBeTrue();

    $result = $following->acceptConnectionRequest($follower);

    expect($result)->toBeTrue();
    expect($connection->fresh()->isAccepted())->toBeTrue();
    expect($connection->fresh()->accepted_at)->not->toBeNull();
});

test('user can decline connection request', function () {
    $follower = User::factory()->create();
    $following = User::factory()->create();

    UserConnection::factory()->pending()->create([
        'follower_id' => $follower->id,
        'following_id' => $following->id,
    ]);

    $result = $following->declineConnectionRequest($follower);

    expect($result)->toBeTrue();
    expect(UserConnection::where('follower_id', $follower->id)
        ->where('following_id', $following->id)
        ->exists())->toBeFalse();
});

test('user can remove connection', function () {
    $follower = User::factory()->create();
    $following = User::factory()->create();

    UserConnection::factory()->accepted()->create([
        'follower_id' => $follower->id,
        'following_id' => $following->id,
    ]);

    $result = $follower->removeConnection($following);

    expect($result)->toBeTrue();
    expect(UserConnection::where('follower_id', $follower->id)
        ->where('following_id', $following->id)
        ->exists())->toBeFalse();
});

test('user can block another user', function () {
    $blocker = User::factory()->create();
    $blocked = User::factory()->create();

    $connection = $blocker->blockUser($blocked);

    expect($connection)->toBeInstanceOf(UserConnection::class);
    expect($connection->status)->toBe('blocked');
    expect($blocker->hasBlocked($blocked))->toBeTrue();
});

test('blocking user removes existing connection', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    UserConnection::factory()->accepted()->create([
        'follower_id' => $user1->id,
        'following_id' => $user2->id,
    ]);

    $user1->blockUser($user2);

    expect(UserConnection::where('follower_id', $user1->id)
        ->where('following_id', $user2->id)
        ->where('status', 'accepted')
        ->exists())->toBeFalse();

    expect(UserConnection::where('follower_id', $user1->id)
        ->where('following_id', $user2->id)
        ->where('status', 'blocked')
        ->exists())->toBeTrue();
});

test('user can unblock another user', function () {
    $blocker = User::factory()->create();
    $blocked = User::factory()->create();

    UserConnection::factory()->blocked()->create([
        'follower_id' => $blocker->id,
        'following_id' => $blocked->id,
    ]);

    $result = $blocker->unblockUser($blocked);

    expect($result)->toBeTrue();
    expect($blocker->hasBlocked($blocked))->toBeFalse();
});

test('user can get followers count', function () {
    $user = User::factory()->create();
    $followers = User::factory()->count(5)->create();

    foreach ($followers as $follower) {
        UserConnection::factory()->accepted()->create([
            'follower_id' => $follower->id,
            'following_id' => $user->id,
        ]);
    }

    expect($user->getFollowersCount())->toBe(5);
});

test('user can get following count', function () {
    $user = User::factory()->create();
    $following = User::factory()->count(3)->create();

    foreach ($following as $followedUser) {
        UserConnection::factory()->accepted()->create([
            'follower_id' => $user->id,
            'following_id' => $followedUser->id,
        ]);
    }

    expect($user->getFollowingCount())->toBe(3);
});

test('user can get pending requests count', function () {
    $user = User::factory()->create();
    $requesters = User::factory()->count(2)->create();

    foreach ($requesters as $requester) {
        UserConnection::factory()->pending()->create([
            'follower_id' => $requester->id,
            'following_id' => $user->id,
        ]);
    }

    expect($user->getPendingRequestsCount())->toBe(2);
});

test('user can check if has pending connection with another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    expect($user1->hasPendingConnectionWith($user2))->toBeFalse();

    UserConnection::factory()->pending()->create([
        'follower_id' => $user1->id,
        'following_id' => $user2->id,
    ]);

    expect($user1->hasPendingConnectionWith($user2))->toBeTrue();
    expect($user2->hasPendingConnectionWith($user1))->toBeTrue();
});

test('user can check if connected with another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    expect($user1->isConnectedWith($user2))->toBeFalse();

    // Create mutual connection
    UserConnection::factory()->accepted()->create([
        'follower_id' => $user1->id,
        'following_id' => $user2->id,
    ]);
    UserConnection::factory()->accepted()->create([
        'follower_id' => $user2->id,
        'following_id' => $user1->id,
    ]);

    expect($user1->fresh()->isConnectedWith($user2))->toBeTrue();
    expect($user2->fresh()->isConnectedWith($user1))->toBeTrue();
});

test('user can get connection status with another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    expect($user1->getConnectionStatusWith($user2))->toBeNull();

    UserConnection::factory()->pending()->create([
        'follower_id' => $user1->id,
        'following_id' => $user2->id,
    ]);

    expect($user1->getConnectionStatusWith($user2))->toBe('pending');
});

test('user can get suggested connections', function () {
    $user = User::factory()->create([
        'interested_category_ids' => [1, 2, 3],
    ]);

    // Create users with similar interests
    $similarUsers = User::factory()->count(5)->create([
        'interested_category_ids' => [1, 2, 4],
    ]);

    // Create user without similar interests
    User::factory()->create([
        'interested_category_ids' => [5, 6, 7],
    ]);

    $suggestions = $user->getSuggestedConnections(10);

    expect($suggestions->count())->toBeGreaterThan(0);
    expect($suggestions->count())->toBeLessThanOrEqual(10);
});

test('pending connections are not counted in followers count', function () {
    $user = User::factory()->create();
    $follower = User::factory()->create();

    UserConnection::factory()->pending()->create([
        'follower_id' => $follower->id,
        'following_id' => $user->id,
    ]);

    expect($user->getFollowersCount())->toBe(0);

    // Accept the connection
    $connection = UserConnection::where('follower_id', $follower->id)
        ->where('following_id', $user->id)
        ->first();
    $connection->accept();

    expect($user->getFollowersCount())->toBe(1);
});

test('blocked connections are not counted in followers count', function () {
    $user = User::factory()->create();
    $blocker = User::factory()->create();

    UserConnection::factory()->blocked()->create([
        'follower_id' => $blocker->id,
        'following_id' => $user->id,
    ]);

    expect($user->getFollowersCount())->toBe(0);
});

