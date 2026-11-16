<?php

use App\Mail\CertificateMail;
use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

it('sends the certificate mail with the PDF attached', function () {
    Mail::fake();
    Storage::fake();

    $booking = Booking::factory()->create(['status' => 'confirmed']);
    Storage::put('certificates/test.pdf', '%PDF-1.4 test');
    $booking->update(['certificate_path' => 'certificates/test.pdf']);

    $mail = new CertificateMail($booking->fresh());

    $mail->assertHasAttachment(
        \Illuminate\Mail\Mailables\Attachment::fromStorage('certificates/test.pdf')
            ->as("Teilnahmezertifikat_{$booking->booking_number}.pdf")
            ->withMime('application/pdf')
    );
    $mail->assertSeeInHtml($booking->event->title);
});

it('caps the admin conversion funnel at 100 percent', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $event = Event::factory()->create(['start_date' => now()->subDays(3), 'end_date' => now()->subDays(3)->addHours(2), 'views' => 1]);
    Booking::factory()->count(3)->create(['event_id' => $event->id, 'payment_status' => 'paid']);

    $response = $this->actingAs($admin)->get(route('admin.reporting.index'));

    $response->assertOk();
    $funnel = $response->viewData('conversionFunnel');
    expect($funnel['start_rate'])->toBe(100)
        ->and($funnel['completion_rate'])->toEqual(100)
        ->and($funnel['event_views'])->toBe(1)
        ->and($funnel['bookings_started'])->toBe(3);
});

it('actually sends the certificate when the organizer requests it', function () {
    Mail::fake();
    Storage::fake();

    ['organizer' => $organizer, 'organization' => $organization] = createOrganizerWithOrganization();
    $event = Event::factory()->create(['organization_id' => $organization->id, 'start_date' => now()->subDays(2), 'end_date' => now()->subDays(2)->addHours(3)]);
    $booking = Booking::factory()->create(['event_id' => $event->id, 'status' => 'confirmed']);

    $this->actingAs($organizer)
        ->withSession(['current_organization_id' => $organization->id])
        ->post(route('organizer.events.certificates.email', [$event, $booking]))
        ->assertSessionHas('success');

    Mail::assertSent(CertificateMail::class, fn ($mail) => $mail->hasTo($booking->customer_email));
    expect($booking->fresh()->certificate_path)->not->toBeNull();
});
