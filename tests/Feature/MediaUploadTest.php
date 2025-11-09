<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\TicketType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function booking_can_be_created_with_pending_payment()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 50,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 2,
            ],
            'payment_method' => 'stripe',
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $this->assertDatabaseHas('bookings', [
            'customer_email' => 'john@example.com',
            'payment_status' => 'pending',
        ]);
    }

    /** @test */
    public function booking_status_changes_after_payment_confirmation()
    {
        $booking = Booking::factory()->create([
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Simulate payment confirmation
        $booking->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
            'payment_transaction_id' => 'txn_' . uniqid(),
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);
    }

    /** @test */
    public function free_events_dont_require_payment()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_date' => now()->addWeek(),
        ]);
        $ticketType = TicketType::factory()->create([
            'event_id' => $event->id,
            'price' => 0,
        ]);

        $bookingData = [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'tickets' => [
                $ticketType->id => 1,
            ],
        ];

        $response = $this->actingAs($user)->post(route('bookings.store', $event), $bookingData);

        $booking = Booking::where('customer_email', 'john@example.com')->first();

        $this->assertEquals(0, $booking->total);
        $this->assertEquals('confirmed', $booking->status);
    }

    /** @test */
    public function refund_can_be_processed_for_cancelled_booking()
    {
        $booking = Booking::factory()->create([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'payment_method' => 'stripe',
            'payment_transaction_id' => 'txn_123',
            'total' => 100,
        ]);

        // Cancel and refund
        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'payment_status' => 'refunded',
        ]);
    }
}

