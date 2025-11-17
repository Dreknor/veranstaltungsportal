<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_upload_event_featured_image()
    {
        Storage::fake('public');

        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $file = UploadedFile::fake()->image('event-image.jpg');

        $response = $this->actingAs($organizer)->post(route('organizer.events.upload-image', $event), [
            'image' => $file,
        ]);

        Storage::disk('public')->assertExists('events/' . $file->hashName());
    }

    #[Test]
    public function organizer_can_upload_gallery_images()
    {
        Storage::fake('public');

        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $files = [
            UploadedFile::fake()->image('gallery1.jpg'),
            UploadedFile::fake()->image('gallery2.jpg'),
            UploadedFile::fake()->image('gallery3.jpg'),
        ];

        $response = $this->actingAs($organizer)->post(route('organizer.events.upload-gallery', $event), [
            'images' => $files,
        ]);

        foreach ($files as $file) {
            Storage::disk('public')->assertExists('events/gallery/' . $file->hashName());
        }
    }

    #[Test]
    public function user_can_upload_profile_photo()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('profile.jpg');

        $response = $this->actingAs($user)->post(route('profile.upload-photo'), [
            'photo' => $file,
        ]);

        Storage::disk('public')->assertExists('profile-photos/' . $file->hashName());
    }

    #[Test]
    public function only_images_can_be_uploaded()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($organizer)->post(route('organizer.events.upload-image', $event), [
            'image' => $file,
        ]);

        $response->assertSessionHasErrors('image');
    }

    #[Test]
    public function image_size_is_limited()
    {
        $organizer = User::factory()->create(['user_type' => 'organizer']);
        $event = Event::factory()->create(['user_id' => $organizer->id]);

        $file = UploadedFile::fake()->image('large-image.jpg')->size(10000); // 10MB

        $response = $this->actingAs($organizer)->post(route('organizer.events.upload-image', $event), [
            'image' => $file,
        ]);

        $response->assertSessionHasErrors('image');
    }
}




