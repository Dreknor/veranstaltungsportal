<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_upload_event_featured_image_on_create()
    {
        Storage::fake('public');

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $category = EventCategory::factory()->create();

        $file = UploadedFile::fake()->image('event-image.jpg');

        $eventData = [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'event_type' => 'physical',
            'event_category_id' => $category->id,
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'end_date' => now()->addWeek()->addHours(2)->format('Y-m-d H:i:s'),
            'venue_name' => 'Test Venue',
            'venue_address' => 'Test Address',
            'venue_city' => 'Berlin',
            'venue_postal_code' => '10115',
            'venue_country' => 'Germany',
            'is_published' => false,
            'is_featured' => false,
            'is_private' => false,
            'featured_image' => $file,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), $eventData);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('events/' . $file->hashName());
    }

    #[Test]
    public function organizer_can_update_event_featured_image()
    {
        Storage::fake('public');

        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $event = Event::factory()->create(['organization_id' => $result['organization']->id]);

        $file = UploadedFile::fake()->image('new-event-image.jpg');

        $updateData = [
            'title' => $event->title,
            'description' => $event->description,
            'event_type' => $event->event_type,
            'event_category_id' => $event->event_category_id,
            'start_date' => $event->start_date->format('Y-m-d H:i:s'),
            'end_date' => $event->end_date->format('Y-m-d H:i:s'),
            'venue_name' => $event->venue_name,
            'venue_address' => $event->venue_address ?? 'Test Address',
            'venue_city' => $event->venue_city,
            'venue_postal_code' => $event->venue_postal_code ?? '10115',
            'venue_country' => $event->venue_country ?? 'Germany',
            'is_published' => false,
            'is_featured' => false,
            'is_private' => false,
            'featured_image' => $file,
        ];

        $response = $this->actingAs($organizer)->put(route('organizer.events.update', $event), $updateData);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('events/' . $file->hashName());
    }

    #[Test]
    public function only_images_can_be_uploaded()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $category = EventCategory::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $eventData = [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'event_type' => 'physical',
            'event_category_id' => $category->id,
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'end_date' => now()->addWeek()->addHours(2)->format('Y-m-d H:i:s'),
            'venue_name' => 'Test Venue',
            'venue_address' => 'Test Address',
            'venue_city' => 'Berlin',
            'venue_postal_code' => '10115',
            'venue_country' => 'Germany',
            'is_published' => false,
            'is_featured' => false,
            'is_private' => false,
            'featured_image' => $file,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), $eventData);

        $response->assertSessionHasErrors('featured_image');
    }

    #[Test]
    public function image_size_is_limited()
    {
        $organizer = User::factory()->create();
        $organizer->assignRole('organizer');
        $result = $this->createOrganizerWithOrganization($organizer);
        $category = EventCategory::factory()->create();

        $file = UploadedFile::fake()->image('large-image.jpg')->size(3000); // 3MB > 2MB limit

        $eventData = [
            'title' => 'Test Event',
            'description' => 'Test Description',
            'event_type' => 'physical',
            'event_category_id' => $category->id,
            'start_date' => now()->addWeek()->format('Y-m-d H:i:s'),
            'end_date' => now()->addWeek()->addHours(2)->format('Y-m-d H:i:s'),
            'venue_name' => 'Test Venue',
            'venue_address' => 'Test Address',
            'venue_city' => 'Berlin',
            'venue_postal_code' => '10115',
            'venue_country' => 'Germany',
            'is_published' => false,
            'is_featured' => false,
            'is_private' => false,
            'featured_image' => $file,
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.store'), $eventData);

        $response->assertSessionHasErrors('featured_image');
    }
}




