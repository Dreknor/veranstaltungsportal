<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ImpersonateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;
    protected User $organizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->user = User::factory()->create();

        $this->organizer = User::factory()->create();
        $this->organizer->assignRole('organizer');
    }

    public function test_impersonate_requires_authentication(): void
    {
        $response = $this->post(route('admin.users.impersonate', $this->user));
        $response->assertRedirect(route('login'));
    }

    public function test_impersonate_requires_admin(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.users.impersonate', $this->organizer));

        $response->assertStatus(403);
    }

    public function test_admin_can_impersonate_user(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.impersonate', $this->user));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('impersonator', $this->admin->id);

        // Check that we're now authenticated as the user
        $this->assertEquals($this->user->id, Auth::id());
    }

    public function test_admin_can_impersonate_organizer(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.impersonate', $this->organizer));

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals($this->organizer->id, Auth::id());
    }

    public function test_cannot_impersonate_self(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.impersonate', $this->admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cannot_impersonate_another_admin(): void
    {
        $anotherAdmin = User::factory()->create();
        $anotherAdmin->assignRole('admin');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.impersonate', $anotherAdmin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_impersonation_logs_audit_entry(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.impersonate', $this->user));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->admin->id,
            'action' => 'impersonate_start',
            'auditable_type' => User::class,
            'auditable_id' => $this->user->id,
        ]);
    }

    public function test_can_leave_impersonation(): void
    {
        // Start impersonation
        session(['impersonator' => $this->admin->id]);
        Auth::login($this->user);

        $response = $this->actingAs($this->user)
            ->post(route('impersonate.leave'));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionMissing('impersonator');

        // Check that we're back as admin
        $this->assertEquals($this->admin->id, Auth::id());
    }

    public function test_leaving_impersonation_logs_audit_entry(): void
    {
        session(['impersonator' => $this->admin->id]);
        Auth::login($this->user);

        $this->actingAs($this->user)
            ->post(route('impersonate.leave'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'impersonate_end',
            'auditable_type' => User::class,
            'auditable_id' => $this->user->id,
        ]);
    }

    public function test_cannot_leave_impersonation_if_not_impersonating(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('impersonate.leave'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_impersonation_session_is_cleared_on_session_flush(): void
    {
        session(['impersonator' => $this->admin->id]);
        Auth::login($this->user);

        // Flush session (this happens on logout in real application)
        session()->flush();

        $this->assertFalse(session()->has('impersonator'));
    }
}

