<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
        $this->user = User::factory()->create();
    }

    public function test_audit_logs_index_requires_authentication(): void
    {
        $response = $this->get(route('admin.audit-logs.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_audit_logs_index_requires_admin(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.audit-logs.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_audit_logs(): void
    {
        AuditLog::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.audit-logs.index');
        $response->assertViewHas('logs');
    }

    public function test_audit_logs_can_be_filtered_by_action(): void
    {
        AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'created',
            'ip_address' => '127.0.0.1',
        ]);

        AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'deleted',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', ['action' => 'created']));

        $response->assertStatus(200);
        $logs = $response->viewData('logs');
        $this->assertCount(1, $logs);
        $this->assertEquals('created', $logs->first()->action);
    }

    public function test_audit_logs_can_be_filtered_by_date(): void
    {
        AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'test',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(10),
        ]);

        AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'test',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.index', [
            'from' => now()->subDays(5)->format('Y-m-d'),
        ]));

        $response->assertStatus(200);
        $logs = $response->viewData('logs');
        $this->assertCount(1, $logs);
    }

    public function test_audit_log_can_be_viewed(): void
    {
        $log = AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'test',
            'ip_address' => '127.0.0.1',
            'description' => 'Test log entry',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.show', $log));

        $response->assertStatus(200);
        $response->assertViewIs('admin.audit-logs.show');
        $response->assertViewHas('auditLog', $log);
    }

    public function test_audit_log_can_be_deleted(): void
    {
        $log = AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'test',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.audit-logs.destroy', $log));

        $response->assertRedirect();
        $this->assertDatabaseMissing('audit_logs', ['id' => $log->id]);
    }

    public function test_old_audit_logs_can_be_cleared(): void
    {
        // Create old logs
        AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'old',
            'ip_address' => '127.0.0.1',
            'created_at' => now()->subDays(60),
        ]);

        // Create recent log
        AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'recent',
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.audit-logs.clear'), [
            'older_than' => 30,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseMissing('audit_logs', ['action' => 'old']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'recent']);
    }

    public function test_audit_logs_can_be_exported(): void
    {
        AuditLog::create([
            'user_id' => $this->user->id,
            'action' => 'test',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.audit-logs.export'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_audit_log_records_user_action(): void
    {
        $this->actingAs($this->user);

        $log = AuditLog::log('test_action', null, null, null, 'Test description');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'action' => 'test_action',
            'description' => 'Test description',
        ]);
    }

    public function test_audit_log_captures_ip_and_user_agent(): void
    {
        $this->actingAs($this->user);

        $log = AuditLog::log('test_action');

        $this->assertNotNull($log->ip_address);
        $this->assertNotNull($log->user_agent);
    }
}

