<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\UpdateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class UpdateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    /** Simulierte Ausgaben, Schlüssel = Präfix des Befehls. */
    protected array $responses = [];

    /** Befehle (Präfixe), die mit Exit-Code 1 fehlschlagen. */
    protected array $failing = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->user = User::factory()->create();

        $this->responses = [
            'git rev-parse --abbrev-ref HEAD' => "master\n",
            'git rev-parse HEAD' => "aaaaaaa1111111111111111111111111111111\n",
            'git rev-parse origin/master' => "bbbbbbb2222222222222222222222222222222\n",
            'git log -1' => "2026-10-01T10:00:00+02:00\n",
            'git log' => "bbbbbbb2222\x1fbbbbbbb\x1fNeue Funktion\x1fDreknor\x1f2026-10-10T12:00:00+02:00\n",
            'git rev-list' => "0\n",
            'git status' => '',
            'git diff' => "app/Models/Event.php\nresources/css/app.css\n",
        ];

        Process::fake(function (PendingProcess $process) {
            $command = $this->normalize($process->command);

            foreach ($this->failing as $prefix) {
                if (str_starts_with($command, $prefix)) {
                    return Process::result('', 'Fehler', 1);
                }
            }

            foreach ($this->responses as $prefix => $output) {
                if (str_starts_with($command, $prefix)) {
                    return Process::result($output);
                }
            }

            return Process::result('');
        });

        config([
            'updater.git_binary' => 'git',
            'updater.php_binary' => 'php',
            'updater.npm_binary' => 'npm',
            'updater.composer_binary' => 'composer',
        ]);
    }

    protected function tearDown(): void
    {
        $updater = new UpdateService;
        File::delete([$updater->statePath(), $updater->logPath()]);

        parent::tearDown();
    }

    /**
     * Macht Befehle vergleichbar: "php /pfad/artisan migrate" → "artisan migrate".
     */
    protected function normalize(array|string $command): string
    {
        $parts = (array) $command;

        if (($parts[0] ?? null) === 'php' && isset($parts[1]) && str_ends_with($parts[1], 'artisan')) {
            $parts = ['artisan', ...array_slice($parts, 2)];
        }

        return implode(' ', $parts);
    }

    protected function assertRanCommand(string $prefix): void
    {
        Process::assertRan(fn (PendingProcess $process) => str_starts_with($this->normalize($process->command), $prefix));
    }

    protected function assertDidntRunCommand(string $prefix): void
    {
        Process::assertDidntRun(fn (PendingProcess $process) => str_starts_with($this->normalize($process->command), $prefix));
    }

    public function test_updates_page_requires_admin(): void
    {
        $this->get(route('admin.updates.index'))->assertRedirect(route('login'));
        $this->actingAs($this->user)->get(route('admin.updates.index'))->assertStatus(403);
        $this->actingAs($this->user)->post(route('admin.updates.store'))->assertStatus(403);
    }

    public function test_admin_sees_available_commits(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.updates.index'))
            ->assertOk()
            ->assertSee('aaaaaaa')
            ->assertSee('Neue Funktion')
            ->assertSee('Jetzt aktualisieren');
    }

    public function test_updates_follow_checked_out_branch_without_configured_branch(): void
    {
        config(['updater.branch' => null]);
        $this->responses = [
            'git rev-parse --abbrev-ref HEAD' => "feature-x\n",
            'git rev-parse origin/feature-x' => "bbbbbbb2222222222222222222222222222222\n",
        ] + $this->responses;

        $this->actingAs($this->admin)
            ->get(route('admin.updates.index'))
            ->assertOk()
            ->assertSee('origin/feature-x')
            ->assertSee('Jetzt aktualisieren');

        $this->assertRanCommand('git fetch --quiet origin feature-x');
        $this->assertRanCommand('git log --format=%H%x1f%h%x1f%s%x1f%an%x1f%cI HEAD..origin/feature-x');
    }

    public function test_configured_branch_takes_precedence(): void
    {
        config(['updater.branch' => 'master']);
        $this->responses = ['git rev-parse --abbrev-ref HEAD' => "feature-x\n"] + $this->responses;

        $this->actingAs($this->admin)->get(route('admin.updates.index'))->assertOk()->assertSee('origin/master');

        $this->assertRanCommand('git fetch --quiet origin master');
    }

    public function test_admin_sees_error_when_git_fails(): void
    {
        $this->failing = ['git fetch'];

        $this->actingAs($this->admin)
            ->get(route('admin.updates.index'))
            ->assertOk()
            ->assertSee('Versionsstand konnte nicht ermittelt werden');
    }

    public function test_admin_can_start_update_in_background(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.updates.store'))
            ->assertRedirect(route('admin.updates.index'));

        Process::assertRan(fn (PendingProcess $process) => str_contains(implode(' ', (array) $process->command), 'app:update'));
        $this->assertSame(UpdateService::STATUS_QUEUED, app(UpdateService::class)->state()['status']);
    }

    public function test_update_cannot_be_started_twice(): void
    {
        app(UpdateService::class)->startInBackground();

        $this->actingAs($this->admin)
            ->from(route('admin.updates.index'))
            ->post(route('admin.updates.store'))
            ->assertSessionHas('error');
    }

    public function test_update_cannot_be_started_when_disabled(): void
    {
        config(['updater.enabled' => false]);

        $this->actingAs($this->admin)->post(route('admin.updates.store'))->assertStatus(403);
    }

    public function test_progress_returns_state_and_log(): void
    {
        app(UpdateService::class)->startInBackground();

        $this->actingAs($this->admin)
            ->getJson(route('admin.updates.progress'))
            ->assertOk()
            ->assertJsonPath('running', true)
            ->assertJsonPath('state.status', UpdateService::STATUS_QUEUED);
    }

    public function test_update_runs_all_steps(): void
    {
        $this->assertTrue(app(UpdateService::class)->update());

        $this->assertRanCommand('artisan down');
        $this->assertRanCommand('git merge --ff-only origin/master');
        $this->assertRanCommand('composer install');
        $this->assertRanCommand('artisan migrate --force');
        $this->assertRanCommand('npm run build');
        $this->assertRanCommand('artisan optimize:clear');
        $this->assertRanCommand('artisan up');

        $updater = app(UpdateService::class);
        $this->assertSame(UpdateService::STATUS_SUCCESS, $updater->state()['status']);
        $this->assertStringContainsString('Update erfolgreich abgeschlossen', $updater->log());
    }

    public function test_update_skips_asset_build_without_frontend_changes(): void
    {
        $this->responses['git diff'] = "app/Models/Event.php\n";

        $this->assertTrue(app(UpdateService::class)->update());

        $this->assertDidntRunCommand('npm');
    }

    public function test_update_does_nothing_when_up_to_date(): void
    {
        $this->responses['git rev-parse origin/master'] = $this->responses['git rev-parse HEAD'];

        $this->assertTrue(app(UpdateService::class)->update());

        $this->assertDidntRunCommand('git merge');
        $this->assertDidntRunCommand('artisan down');
    }

    public function test_update_aborts_on_local_changes(): void
    {
        $this->responses['git status'] = " M app/Models/Event.php\n";

        $this->assertFalse(app(UpdateService::class)->update());

        $this->assertDidntRunCommand('git merge');
        $this->assertStringContainsString('app/Models/Event.php', app(UpdateService::class)->log());
    }

    public function test_failed_step_marks_update_failed_and_leaves_maintenance_mode(): void
    {
        $this->failing = ['artisan migrate'];

        $updater = app(UpdateService::class);
        $this->assertFalse($updater->update());

        $this->assertRanCommand('artisan up');
        $this->assertDidntRunCommand('artisan optimize:clear');
        $this->assertSame(UpdateService::STATUS_FAILED, $updater->state()['status']);
        $this->assertStringContainsString('Datenbank migrieren', $updater->log());
    }
}
