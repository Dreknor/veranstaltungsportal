<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class NewsletterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Create regular user
        $this->user = User::factory()->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);
    }

    /** @test */
    public function admin_can_access_newsletter_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.newsletter.index');
        $response->assertViewHas(['stats', 'upcomingEvents', 'featuredEvents', 'recentNewsletters']);
    }

    /** @test */
    public function non_admin_cannot_access_newsletter_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('admin.newsletter.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_cannot_access_newsletter_index()
    {
        $response = $this->get(route('admin.newsletter.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function newsletter_index_displays_correct_statistics()
    {
        // Create additional subscribers
        User::factory()->count(5)->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        // Create guest subscribers
        DB::table('newsletter_subscribers')->insert([
            ['email' => 'guest1@example.com', 'subscribed_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            ['email' => 'guest2@example.com', 'subscribed_at' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.index'));

        $response->assertStatus(200);

        $stats = $response->viewData('stats');
        $this->assertEquals(6, $stats['total_subscribers']); // 1 + 5
        $this->assertEquals(2, $stats['guest_subscribers']);
    }

    /** @test */
    public function admin_can_access_newsletter_compose_page()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.compose', ['type' => 'weekly']));

        $response->assertStatus(200);
        $response->assertViewIs('admin.newsletter.compose');
        $response->assertViewHas(['type', 'upcomingEvents', 'featuredEvents', 'recommendations', 'sampleUser']);
    }

    /** @test */
    public function compose_page_uses_weekly_as_default_type()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.compose'));

        $response->assertStatus(200);
        $this->assertEquals('weekly', $response->viewData('type'));
    }

    /** @test */
    public function compose_page_accepts_monthly_type()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.compose', ['type' => 'monthly']));

        $response->assertStatus(200);
        $this->assertEquals('monthly', $response->viewData('type'));
    }

    /** @test */
    public function admin_can_preview_newsletter()
    {
        $category = EventCategory::factory()->create();
        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'is_featured' => true,
            'start_date' => now()->addDays(7),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.preview', ['type' => 'weekly']));

        $response->assertStatus(200);
        $response->assertViewIs('emails.newsletter');
        $response->assertViewHas(['subscriber', 'upcomingEvents', 'featuredEvents', 'recommendations', 'type']);
    }

    /** @test */
    public function admin_can_send_test_newsletter()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.newsletter.send'), [
                'type' => 'weekly',
                'send_to' => 'test',
            ]);

        $response->assertRedirect(route('admin.newsletter.index'));
        $response->assertSessionHas('success', 'Test-Newsletter wurde an alle Admins versendet!');
    }

    /** @test */
    public function send_newsletter_requires_valid_type()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.newsletter.send'), [
                'type' => 'invalid',
                'send_to' => 'test',
            ]);

        $response->assertSessionHasErrors('type');
    }

    /** @test */
    public function send_newsletter_requires_valid_send_to()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.newsletter.send'), [
                'type' => 'weekly',
                'send_to' => 'invalid',
            ]);

        $response->assertSessionHasErrors('send_to');
    }

    /** @test */
    public function admin_can_view_subscribers_list()
    {
        // Create subscribers
        User::factory()->count(3)->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.subscribers'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.newsletter.subscribers');
        $response->assertViewHas(['subscribers', 'guestSubscribers']);
    }

    /** @test */
    public function subscribers_list_paginates_results()
    {
        // Create more than 50 subscribers
        User::factory()->count(60)->create([
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.subscribers'));

        $response->assertStatus(200);

        $subscribers = $response->viewData('subscribers');
        $this->assertEquals(50, $subscribers->perPage());
        $this->assertEquals(61, $subscribers->total()); // 60 + 1 from setUp
    }

    /** @test */
    public function admin_can_export_subscribers_csv()
    {
        User::factory()->count(3)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        DB::table('newsletter_subscribers')->insert([
            ['email' => 'guest@example.com', 'subscribed_at' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('newsletter_subscribers_', $response->headers->get('Content-Disposition'));
    }

    /** @test */
    public function exported_csv_contains_correct_headers()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.export'));

        $content = $response->streamedContent();

        $this->assertStringContainsString('E-Mail', $content);
        $this->assertStringContainsString('Vorname', $content);
        $this->assertStringContainsString('Nachname', $content);
        $this->assertStringContainsString('Typ', $content);
        $this->assertStringContainsString('Abonniert seit', $content);
    }

    /** @test */
    public function exported_csv_includes_registered_and_guest_subscribers()
    {
        User::factory()->create([
            'email' => 'registered@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'newsletter_subscribed' => true,
            'newsletter_subscribed_at' => now(),
        ]);

        DB::table('newsletter_subscribers')->insert([
            ['email' => 'guest@example.com', 'subscribed_at' => now(), 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.export'));

        $content = $response->streamedContent();

        $this->assertStringContainsString('registered@example.com', $content);
        $this->assertStringContainsString('guest@example.com', $content);
        $this->assertStringContainsString('Registriert', $content);
        $this->assertStringContainsString('Gast', $content);
    }

    /** @test */
    public function newsletter_index_shows_upcoming_events()
    {
        $category = EventCategory::factory()->create();

        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->addDays(7),
            'title' => 'Future Event',
        ]);

        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'start_date' => now()->subDays(7),
            'title' => 'Past Event',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.index'));

        $upcomingEvents = $response->viewData('upcomingEvents');

        $this->assertEquals(1, $upcomingEvents->count());
        $this->assertEquals('Future Event', $upcomingEvents->first()->title);
    }

    /** @test */
    public function newsletter_index_shows_featured_events()
    {
        $category = EventCategory::factory()->create();

        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'is_featured' => true,
            'start_date' => now()->addDays(7),
            'title' => 'Featured Event',
        ]);

        Event::factory()->create([
            'event_category_id' => $category->id,
            'is_published' => true,
            'is_featured' => false,
            'start_date' => now()->addDays(7),
            'title' => 'Regular Event',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.newsletter.index'));

        $featuredEvents = $response->viewData('featuredEvents');

        $this->assertEquals(1, $featuredEvents->count());
        $this->assertEquals('Featured Event', $featuredEvents->first()->title);
    }
}

