<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConnectionManagementControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_connections_index(): void
    {
        UserConnection::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.connections.index'));

        $response->assertOk();
        $response->assertViewIs('admin.connections.index');
        $response->assertViewHas(['connections', 'stats']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_view_connections_index(): void
    {
        $user = User::factory()->create();
        // User has no admin role

        $response = $this->actingAs($user)->get(route('admin.connections.index'));

        $response->assertStatus(403); // Forbidden because AdminMiddleware blocks non-admins
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_filter_connections_by_status(): void
    {
        $pendingConnection = UserConnection::factory()->create(['status' => 'pending']);
        $acceptedConnection = UserConnection::factory()->create(['status' => 'accepted']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.connections.index', ['status' => 'pending']));

        $response->assertOk();
        $response->assertSee($pendingConnection->follower->name);
        $response->assertDontSee($acceptedConnection->follower->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_search_connections(): void
    {
        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);

        $connection1 = UserConnection::factory()->create([
            'follower_id' => $user1->id,
        ]);

        $connection2 = UserConnection::factory()->create([
            'follower_id' => $user2->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.connections.index', ['search' => 'John']));

        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_connection_details(): void
    {
        $connection = UserConnection::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.connections.show', $connection));

        $response->assertOk();
        $response->assertViewIs('admin.connections.show');
        $response->assertSee($connection->follower->name);
        $response->assertSee($connection->following->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_connection_status(): void
    {
        $connection = UserConnection::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.connections.update-status', $connection), [
                'status' => 'accepted',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_connections', [
            'id' => $connection->id,
            'status' => 'accepted',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_delete_connection(): void
    {
        $connection = UserConnection::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.connections.destroy', $connection));

        $response->assertRedirect(route('admin.connections.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('user_connections', ['id' => $connection->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_statistics(): void
    {
        UserConnection::factory()->count(10)->create(['status' => 'accepted']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.connections.statistics'));

        $response->assertOk();
        $response->assertViewIs('admin.connections.statistics');
        $response->assertViewHas(['connectionsByDay', 'byStatus', 'mostActiveUsers']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_perform_bulk_approve(): void
    {
        $connections = UserConnection::factory()->count(3)->create(['status' => 'pending']);
        $connectionIds = $connections->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.connections.bulk'), [
                'action' => 'approve',
                'connection_ids' => $connectionIds,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($connectionIds as $connectionId) {
            $this->assertDatabaseHas('user_connections', [
                'id' => $connectionId,
                'status' => 'accepted',
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_perform_bulk_block(): void
    {
        $connections = UserConnection::factory()->count(3)->create(['status' => 'accepted']);
        $connectionIds = $connections->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.connections.bulk'), [
                'action' => 'block',
                'connection_ids' => $connectionIds,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($connectionIds as $connectionId) {
            $this->assertDatabaseHas('user_connections', [
                'id' => $connectionId,
                'status' => 'blocked',
            ]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_perform_bulk_delete(): void
    {
        $connections = UserConnection::factory()->count(3)->create();
        $connectionIds = $connections->pluck('id')->toArray();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.connections.bulk'), [
                'action' => 'delete',
                'connection_ids' => $connectionIds,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($connectionIds as $connectionId) {
            $this->assertDatabaseMissing('user_connections', ['id' => $connectionId]);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function bulk_action_requires_valid_action(): void
    {
        $connection = UserConnection::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.connections.bulk'), [
                'action' => 'invalid',
                'connection_ids' => [$connection->id],
            ]);

        $response->assertSessionHasErrors('action');
    }
}

