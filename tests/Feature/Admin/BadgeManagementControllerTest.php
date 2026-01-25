<?php

namespace Tests\Feature\Admin;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BadgeManagementControllerTest extends TestCase
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
    public function admin_can_view_badges_index(): void
    {
        Badge::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.badges.index'));

        $response->assertOk();
        $response->assertViewIs('admin.badges.index');
        $response->assertViewHas(['badges', 'stats']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function non_admin_cannot_view_badges_index(): void
    {
        $user = User::factory()->create();
        // User has no admin role

        $response = $this->actingAs($user)->get(route('admin.badges.index'));

        $response->assertStatus(403); // Forbidden because AdminMiddleware blocks non-admins
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_filter_badges_by_type(): void
    {
        $attendanceBadge = Badge::factory()->create(['type' => 'attendance']);
        $achievementBadge = Badge::factory()->create(['type' => 'achievement']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.badges.index', ['type' => 'attendance']));

        $response->assertOk();
        $response->assertSee($attendanceBadge->name);
        $response->assertDontSee($achievementBadge->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_create_form(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.badges.create'));

        $response->assertOk();
        $response->assertViewIs('admin.badges.create');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_create_badge(): void
    {
        $badgeData = [
            'name' => 'Test Badge',
            'description' => 'This is a test badge',
            'type' => 'achievement',
            'icon' => 'fas fa-trophy',
            'color' => '#FFD700',
            'points' => 50,
            'requirement_type' => 'events_attended',
            'requirement_value' => 5,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.badges.store'), $badgeData);

        $response->assertRedirect(route('admin.badges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('badges', [
            'name' => 'Test Badge',
            'type' => 'achievement',
            'points' => 50,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function badge_creation_requires_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.badges.store'), []);

        $response->assertSessionHasErrors([
            'name',
            'description',
            'type',
            'icon',
            'color',
            'points',
            'requirement_type',
            'requirement_value',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_badge_details(): void
    {
        $badge = Badge::factory()->create();
        $user = User::factory()->create();
        $badge->users()->attach($user->id, ['earned_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.badges.show', $badge));

        $response->assertOk();
        $response->assertViewIs('admin.badges.show');
        $response->assertSee($badge->name);
        $response->assertSee($user->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_view_edit_form(): void
    {
        $badge = Badge::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.badges.edit', $badge));

        $response->assertOk();
        $response->assertViewIs('admin.badges.edit');
        $response->assertSee($badge->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_update_badge(): void
    {
        $badge = Badge::factory()->create();

        $updatedData = [
            'name' => 'Updated Badge Name',
            'description' => $badge->description,
            'type' => $badge->type,
            'icon' => $badge->icon,
            'color' => $badge->color,
            'points' => 100,
            'requirement_type' => $badge->requirement_type,
            'requirement_value' => $badge->requirement_value,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.badges.update', $badge), $updatedData);

        $response->assertRedirect(route('admin.badges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('badges', [
            'id' => $badge->id,
            'name' => 'Updated Badge Name',
            'points' => 100,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_delete_badge_without_users(): void
    {
        $badge = Badge::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.badges.destroy', $badge));

        $response->assertRedirect(route('admin.badges.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('badges', ['id' => $badge->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_cannot_delete_badge_with_users(): void
    {
        $badge = Badge::factory()->create();
        $user = User::factory()->create();
        $badge->users()->attach($user->id, ['earned_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.badges.destroy', $badge));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('badges', ['id' => $badge->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_award_badge_to_user(): void
    {
        $badge = Badge::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.badges.award', $badge), [
                'user_id' => $user->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('user_badges', [
            'user_id' => $user->id,
            'badge_id' => $badge->id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_cannot_award_badge_twice_to_same_user(): void
    {
        $badge = Badge::factory()->create();
        $user = User::factory()->create();
        $badge->users()->attach($user->id, ['earned_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.badges.award', $badge), [
                'user_id' => $user->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function admin_can_revoke_badge_from_user(): void
    {
        $badge = Badge::factory()->create();
        $user = User::factory()->create();
        $badge->users()->attach($user->id, ['earned_at' => now()]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.badges.revoke', $badge), [
                'user_id' => $user->id,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('user_badges', [
            'user_id' => $user->id,
            'badge_id' => $badge->id,
        ]);
    }
}

