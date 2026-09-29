<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitemapControllerTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function sitemap_index_returns_xml(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml', false);
        $response->assertSee('sitemapindex');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function static_sitemap_returns_xml(): void
    {
        $response = $this->get(route('sitemap.static'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml', false);
        $response->assertSee('urlset');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function static_sitemap_contains_main_routes(): void
    {
        $response = $this->get(route('sitemap.static'));

        $response->assertOk();
        $response->assertSee(route('home'));
        $response->assertSee(route('events.index'));
        $response->assertSee(route('events.calendar'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function events_sitemap_returns_xml(): void
    {
        $response = $this->get(route('sitemap.events'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml', false);
        $response->assertSee('urlset');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function events_sitemap_contains_published_events(): void
    {
        $publishedEvent = \App\Models\Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(10)->addHours(2),
        ]);

        $unpublishedEvent = \App\Models\Event::factory()->create([
            'is_published' => false,
        ]);

        $response = $this->get(route('sitemap.events'));

        $response->assertOk();
        $response->assertSee(route('events.show', $publishedEvent->slug));
        $response->assertDontSee(route('events.show', $unpublishedEvent->slug));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function categories_sitemap_returns_xml(): void
    {
        $response = $this->get(route('sitemap.categories'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml', false);
        $response->assertSee('urlset');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function categories_sitemap_contains_active_categories(): void
    {
        $activeCategory = \App\Models\EventCategory::factory()->create([
            'is_active' => true,
        ]);

        $inactiveCategory = \App\Models\EventCategory::factory()->create([
            'is_active' => false,
        ]);

        $response = $this->get(route('sitemap.categories'));

        $response->assertOk();
        $response->assertSee($activeCategory->slug);
        $response->assertDontSee($inactiveCategory->slug);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function organizers_sitemap_returns_xml(): void
    {
        $response = $this->get(route('sitemap.organizers'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<?xml', false);
        $response->assertSee('urlset');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function organizers_sitemap_contains_organizers_with_published_events(): void
    {
        $organizerWithEvents = \App\Models\User::factory()->create(['is_organizer' => true]);
        $organization = \App\Models\Organization::factory()->create();
        $organization->users()->attach($organizerWithEvents->id, ['role' => 'owner', 'is_active' => true, 'joined_at' => now()]);
        \App\Models\Event::factory()->create([
            'organization_id' => $organization->id,
            'is_published' => true,
        ]);

        $organizerWithoutEvents = \App\Models\User::factory()->create(['is_organizer' => true]);

        $response = $this->get(route('sitemap.organizers'));

        $response->assertOk();
        $response->assertSee(route('users.show', $organizerWithEvents->id));
        $response->assertDontSee(route('users.show', $organizerWithoutEvents->id));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function robots_txt_returns_plain_text(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=utf-8');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function robots_txt_contains_sitemap_url(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertSee('Sitemap: ' . url('/sitemap.xml'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function robots_txt_disallows_admin_area(): void
    {
        $response = $this->get(route('robots'));

        $response->assertOk();
        $response->assertSee('Disallow: /admin/');
        $response->assertSee('Disallow: /organizer/');
    }
}

