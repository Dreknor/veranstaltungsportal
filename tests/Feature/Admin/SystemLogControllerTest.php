<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Support\Ui;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SystemLogControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** Der Log-Treiber schreibt den Zeitpunkt als "2026-11-16 17:45:09:1109". */
    private function log(string $datetime, string $level = 'ERROR', string $message = 'Meldung'): void
    {
        DB::table(config('logtodb.collection', 'log'))->insert([
            'message' => $message,
            'channel' => 'local',
            'level' => 400,
            'level_name' => $level,
            'unix_time' => time(),
            'datetime' => $datetime,
        ]);
    }

    public function test_statistics_count_todays_logs_despite_millisecond_suffix(): void
    {
        $this->log(now()->format('Y-m-d H:i:s').':1109');
        $this->log(now()->subDays(3)->format('Y-m-d H:i:s').':1109');

        $response = $this->actingAs($this->admin)->get(route('admin.system-logs.statistics'));

        $response->assertOk();
        $this->assertSame(1, $response->viewData('stats')['today']);
        $this->assertSame(2, $response->viewData('stats')['week']);
    }

    public function test_date_filter_matches_whole_days(): void
    {
        $this->log('2026-11-15 23:59:59:0001', 'INFO', 'gestern spät');
        $this->log('2026-11-16 00:00:00:0001', 'INFO', 'heute früh');
        $this->log('2026-11-16 23:59:59:9999', 'INFO', 'heute spät');
        $this->log('2026-11-17 00:00:01:0001', 'INFO', 'morgen');

        $response = $this->actingAs($this->admin)
            ->get(route('admin.system-logs.index', ['from' => '2026-11-16', 'to' => '2026-11-16']));

        $response->assertOk();
        $this->assertSame(['heute spät', 'heute früh'], $response->viewData('logs')->pluck('message')->all());
    }

    public function test_levels_are_shown_with_german_labels_not_capitals(): void
    {
        $this->log(now()->format('Y-m-d H:i:s').':1109', 'ERROR', 'Etwas ist schiefgelaufen');

        $this->actingAs($this->admin)->get(route('admin.system-logs.index'))
            ->assertOk()
            ->assertSee('Fehler')
            ->assertSee('Etwas ist schiefgelaufen')
            ->assertDontSee('>ERROR<', false);

        $this->assertSame('Fehler', Ui::logLevelLabel('ERROR'));
        $this->assertSame('Information', Ui::logLevelLabel('info'));
        $this->assertSame('Sonstiges', Ui::logLevelLabel('Sonstiges'));
    }

    public function test_statistics_percentages_keep_one_decimal(): void
    {
        $this->assertSame('1,0 %', Ui::percent(1.04, 1, false));
        $this->assertSame('1 %', Ui::percent(1.04));
    }
}
