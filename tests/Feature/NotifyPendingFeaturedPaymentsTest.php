<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\FeaturedEventFee;
use App\Models\Organization;
use App\Models\User;
use App\Notifications\PendingFeaturedPaymentAdminNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotifyPendingFeaturedPaymentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $organizer;
    protected Organization $organization;
    protected Event $event;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.de']);
        $this->admin->assignRole('admin');

        $this->organizer = User::factory()->create(['email' => 'organizer@test.de']);
        $this->organizer->assignRole('organizer');

        $this->organization = Organization::factory()->create();
        $this->organization->users()->attach($this->organizer->id, [
            'role' => 'owner',
            'is_active' => true,
            'joined_at' => now(),
        ]);

        $this->event = Event::factory()->create([
            'user_id' => $this->organizer->id,
            'organization_id' => $this->organization->id,
        ]);
    }

    /**
     * Hilfsmethode: Erstellt eine FeaturedEventFee mit rückwirkend gesetztem created_at.
     */
    private function createPendingFee(int $daysOld, string $paymentStatus = 'pending'): FeaturedEventFee
    {
        $fee = FeaturedEventFee::create([
            'event_id' => $this->event->id,
            'user_id' => $this->organizer->id,
            'duration_type' => 'weekly',
            'duration_days' => 7,
            'featured_start_date' => now()->addDay(),
            'featured_end_date' => now()->addDays(8),
            'fee_amount' => 49.99,
            'payment_status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'paid' ? now()->subDays($daysOld) : null,
        ]);

        // Timestamps rückwirkend setzen (Eloquent überschreibt created_at automatisch)
        DB::table('featured_event_fees')
            ->where('id', $fee->id)
            ->update([
                'created_at' => now()->subDays($daysOld),
                'updated_at' => now()->subDays($daysOld),
            ]);

        return $fee->fresh();
    }

    #[Test]
    public function it_notifies_admin_when_pending_fees_older_than_7_days_exist(): void
    {
        Notification::fake();

        $this->createPendingFee(daysOld: 10);

        // Prüfe ob Admin-User vorhanden
        $this->assertDatabaseHas('users', ['email' => 'admin@test.de']);

        $this->artisan('featured:notify-pending-payments')
            ->assertSuccessful();

        Notification::assertSentTo(
            $this->admin,
            PendingFeaturedPaymentAdminNotification::class
        );
    }

    #[Test]
    public function it_does_not_notify_when_no_pending_fees_exist(): void
    {
        Notification::fake();

        $this->artisan('featured:notify-pending-payments')
            ->assertSuccessful()
            ->expectsOutput('Keine ausstehenden Featured-Zahlungen älter als 7 Tage.');

        Notification::assertNothingSent();
    }

    #[Test]
    public function it_does_not_notify_for_recent_pending_fees(): void
    {
        Notification::fake();

        // Zahlung ist nur 3 Tage alt (unter 7-Tage-Schwelle)
        $this->createPendingFee(daysOld: 3);

        $this->artisan('featured:notify-pending-payments')
            ->assertSuccessful()
            ->expectsOutput('Keine ausstehenden Featured-Zahlungen älter als 7 Tage.');

        Notification::assertNothingSent();
    }

    #[Test]
    public function it_does_not_notify_for_paid_fees(): void
    {
        Notification::fake();

        // Bezahlte Gebühr (auch wenn alt)
        $this->createPendingFee(daysOld: 10, paymentStatus: 'paid');

        $this->artisan('featured:notify-pending-payments')
            ->assertSuccessful()
            ->expectsOutput('Keine ausstehenden Featured-Zahlungen älter als 7 Tage.');

        Notification::assertNothingSent();
    }

    #[Test]
    public function it_supports_custom_days_option(): void
    {
        Notification::fake();

        // Zahlung ist 3 Tage alt
        $this->createPendingFee(daysOld: 3);

        // Mit --days=2 sollte die 3 Tage alte Zahlung gefunden werden
        $this->artisan('featured:notify-pending-payments', ['--days' => 2])
            ->assertSuccessful();

        Notification::assertSentTo(
            $this->admin,
            PendingFeaturedPaymentAdminNotification::class
        );
    }

    #[Test]
    public function dry_run_does_not_send_notifications(): void
    {
        Notification::fake();

        $this->createPendingFee(daysOld: 10);

        $this->artisan('featured:notify-pending-payments', ['--dry-run' => true])
            ->assertSuccessful();

        // Im Dry-Run sollte nichts gesendet werden
        Notification::assertNothingSent();
    }
}


