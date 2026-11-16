<?php

namespace Tests\Feature\Admin;

use App\Models\Badge;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\AccessLabels;
use App\Support\AdminLabels;
use App\Support\Ui;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminOverviewPolishTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    // Zähler in den Listen

    public function test_user_list_shows_event_and_booking_counts(): void
    {
        $organizer = User::factory()->create(['name' => 'Zählerin Veranstalter']);
        $organizer->assignRole('organizer');
        $organization = Organization::factory()->create();
        $organization->users()->attach($organizer->id, ['role' => 'owner', 'is_active' => true, 'joined_at' => now()]);
        Event::factory()->count(3)->create(['organization_id' => $organization->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertOk();
        $listed = $response->viewData('users')->firstWhere('id', $organizer->id);
        $this->assertSame(3, $listed->events_count);
        $this->assertSame(0, $listed->bookings_count);
    }

    public function test_organization_list_counts_events_without_sorting(): void
    {
        $organization = Organization::factory()->create();
        Event::factory()->count(2)->create(['organization_id' => $organization->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.organizations.index'));

        $response->assertOk();
        $this->assertSame(2, $response->viewData('organizations')->firstWhere('id', $organization->id)->events_count);
    }

    public function test_badge_list_counts_awarded_users(): void
    {
        $badge = Badge::factory()->create();
        $badge->users()->attach(User::factory()->count(2)->create()->pluck('id')->all(), ['earned_at' => now()]);

        $response = $this->actingAs($this->admin)->get(route('admin.badges.index'));

        $response->assertOk();
        $this->assertSame(2, $response->viewData('badges')->firstWhere('id', $badge->id)->users_count);
    }

    // Deutsche Anzeigenamen für Rollen und Berechtigungen

    public function test_labels_are_german_and_keys_unchanged(): void
    {
        $this->assertSame('Veranstalter', AccessLabels::role('organizer'));
        $this->assertSame('Administrator', AccessLabels::role('admin'));
        $this->assertSame('Admin-Bereich aufrufen', AccessLabels::permission('access admin panel'));
        $this->assertSame('Allgemein', AccessLabels::group('general'));
        $this->assertSame('Eigene-rolle', AccessLabels::role('eigene-rolle'));
    }

    public function test_role_and_permission_pages_show_german_names_without_monospace(): void
    {
        Permission::create(['name' => 'access admin panel', 'guard_name' => 'web', 'group' => 'general']);

        $roles = $this->actingAs($this->admin)->get(route('admin.roles.index'));
        $roles->assertOk()->assertSee('Veranstalter')->assertSee('Admin-Bereich aufrufen')->assertSee('Allgemein');
        $this->assertStringNotContainsString('font-mono', $roles->getContent());

        foreach (['admin.roles.matrix', 'admin.permissions.index', 'admin.roles.create'] as $route) {
            $page = $this->get(route($route));
            $page->assertOk()->assertSee('Admin-Bereich aufrufen')->assertDontSee('>General<', false);
            $this->assertStringNotContainsString('font-mono', $page->getContent());
        }

        $organizer = Role::where('name', 'organizer')->firstOrFail();
        $this->get(route('admin.roles.edit', $organizer))->assertOk()->assertSee('Rolle bearbeiten: Veranstalter');
    }

    // Rollenfarben nur aus der Palette

    public function test_role_color_must_be_a_palette_tone(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), ['name' => 'farbtest', 'color' => '#3b82f6'])
            ->assertSessionHasErrors('color');

        $this->post(route('admin.roles.store'), ['name' => 'farbtest', 'color' => 'success'])->assertRedirect();

        $this->assertSame('success', Role::where('name', 'farbtest')->value('color'));
    }

    public function test_role_form_offers_only_palette_tones(): void
    {
        $page = $this->actingAs($this->admin)->get(route('admin.roles.create'));

        $page->assertOk()->assertSee('Petrol')->assertSee('Grün')->assertSee('Gelb')->assertSee('Rot');
        $this->assertStringNotContainsString('#3b82f6', $page->getContent());
        $this->assertSame('success', AccessLabels::roleTone('success'));
        $this->assertSame('neutral', AccessLabels::roleTone('#6b7280'));
    }

    public function test_role_color_migration_maps_hex_to_nearest_tone(): void
    {
        $role = Role::create(['name' => 'legacy-rot', 'guard_name' => 'web']);
        DB::table('roles')->where('id', $role->id)->update(['color' => '#ef4444']);
        $green = Role::create(['name' => 'legacy-gruen', 'guard_name' => 'web']);
        DB::table('roles')->where('id', $green->id)->update(['color' => '#10b981']);
        $grey = Role::create(['name' => 'legacy-grau', 'guard_name' => 'web']);
        DB::table('roles')->where('id', $grey->id)->update(['color' => '#6b7280']);

        (include database_path('migrations/2026_11_16_100000_map_role_colors_to_palette_tones.php'))->up();

        $this->assertSame('danger', DB::table('roles')->where('id', $role->id)->value('color'));
        $this->assertSame('success', DB::table('roles')->where('id', $green->id)->value('color'));
        $this->assertSame('neutral', DB::table('roles')->where('id', $grey->id)->value('color'));
    }

    // Typo im Einstellungslabel

    public function test_label_typo_migration_corrects_existing_setting(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'invoice_number_format_booking'],
            ['value' => 'RE-{YEAR}-{NUMBER}', 'type' => 'string', 'label' => 'Rechungsnummer-Format für Veranstalter', 'created_at' => now(), 'updated_at' => now()]
        );

        (include database_path('migrations/2026_11_16_100100_fix_invoice_number_setting_label_typo.php'))->up();

        $this->assertSame('Rechnungsnummer-Format für Veranstalter', DB::table('settings')->where('key', 'invoice_number_format_booking')->value('label'));
    }

    // /admin und 404

    public function test_admin_root_redirects_to_dashboard(): void
    {
        $this->actingAs($this->admin)->get('/admin')->assertRedirect('/admin/dashboard');
    }

    public function test_admin_root_is_protected(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_unknown_page_shows_header_of_signed_in_user(): void
    {
        $user = User::factory()->create(['name' => 'Erika Muster']);

        $this->actingAs($user)->get('/diese-seite-gibt-es-nicht')
            ->assertNotFound()
            ->assertSee('Seite nicht gefunden')
            ->assertDontSee('Registrieren');
    }

    public function test_unknown_page_for_guest_offers_login(): void
    {
        $this->get('/diese-seite-gibt-es-nicht')
            ->assertNotFound()
            ->assertSee('Seite nicht gefunden')
            ->assertSee('Anmelden');
    }

    // Badge-Formular, Anzeigehelfer

    public function test_badge_form_offers_own_icons_and_palette_default(): void
    {
        $page = $this->actingAs($this->admin)->get(route('admin.badges.create'));

        $page->assertOk()->assertSee('Medaille')->assertSee('Datei auswählen')->assertDontSee('fa-medal')->assertDontSee('#3B82F6');
    }

    public function test_legacy_font_awesome_badge_icon_stays_selectable(): void
    {
        $this->assertSame('medal', AdminLabels::badgeIconKey('fas fa-medal'));
        $this->assertArrayHasKey('medal', AdminLabels::badgeIcons('fas fa-medal'));
    }

    public function test_german_admin_labels(): void
    {
        $this->assertSame('Anmeldung', AdminLabels::auditAction('login'));
        $this->assertSame('Bewertung', AdminLabels::auditModel('App\\Models\\EventReview'));
        $this->assertSame('PayPal', AdminLabels::paymentMethod('paypal'));
        $this->assertSame('Präsenz', AdminLabels::eventType('physical'));
        $this->assertSame('5 %', Ui::percent(5.0));
        $this->assertSame('4,5 %', Ui::percent(4.5));
        $this->assertSame('33,3 %', Ui::percent(33.333));
    }

    // Runde 2: Abzeichen-Anforderung und Organisationsseite

    public function test_badge_pages_show_requirement_from_requirements_array(): void
    {
        $badge = Badge::factory()->create(['requirements' => ['events_attended' => 25]]);

        $this->actingAs($this->admin)->get(route('admin.badges.index'))
            ->assertOk()
            ->assertSee('25 Veranstaltungen besucht (eingecheckt)');
        $this->actingAs($this->admin)->get(route('admin.badges.show', $badge))
            ->assertOk()
            ->assertSee('25 Veranstaltungen besucht (eingecheckt)');
    }

    public function test_badge_requirement_summary_handles_aliases_and_empty(): void
    {
        $this->assertSame('3 Bezahlte Buchungen', Badge::factory()->make(['requirements' => ['bookings_made' => 3]])->requirementSummary());
        $this->assertSame('', Badge::factory()->make(['requirements' => null])->requirementSummary());
    }

    public function test_organization_detail_has_no_text_after_layout(): void
    {
        $organization = Organization::factory()->create();

        $html = $this->actingAs($this->admin)->get(route('admin.organizations.show', $organization))
            ->assertOk()
            ->getContent();

        $this->assertDoesNotMatchRegularExpression('~</html>\s*\S~', $html);
        $this->assertStringEndsNotWith(')', rtrim($html));
    }
}
