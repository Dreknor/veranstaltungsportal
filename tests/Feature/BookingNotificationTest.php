<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use App\Notifications\BookingStatusChangedNotification;
use App\Notifications\PaymentStatusChangedNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();

    $this->user = User::factory()->create();
    $this->event = Event::factory()->create();

    $this->booking = Booking::factory()->create([
        'user_id' => $this->user->id,
        'event_id' => $this->event->id,
        'customer_email' => $this->user->email,
        'status' => 'pending',
        'payment_status' => 'pending',
    ]);
});

test('notification is sent when booking status changes', function () {

    $this->booking->status = 'confirmed';
    $this->booking->save();

    Notification::assertSentTo(
        $this->user,
        BookingStatusChangedNotification::class,
        function ($notification) {
            return $notification->toArray($this->user)['new_status'] === 'confirmed';
        }
    );
});

test('notification is sent when payment status changes', function () {
    $this->booking->payment_status = 'paid';
    $this->booking->save();

    Notification::assertSentTo(
        $this->user,
        PaymentStatusChangedNotification::class,
        function ($notification) {
            return $notification->toArray($this->user)['new_payment_status'] === 'paid';
        }
    );
});

test('notification is not sent when status remains the same', function () {
    $this->booking->customer_name = 'New Name';
    $this->booking->save();

    Notification::assertNothingSent();
});

test('notification contains correct old and new status', function () {
    $this->booking->status = 'confirmed';
    $this->booking->save();

    Notification::assertSentTo(
        $this->user,
        BookingStatusChangedNotification::class,
        function ($notification) {
            $data = $notification->toArray($this->user);
            return $data['old_status'] === 'pending'
                && $data['new_status'] === 'confirmed';
        }
    );
});

test('notification contains correct old and new payment status', function () {
    $this->booking->payment_status = 'paid';
    $this->booking->save();

    Notification::assertSentTo(
        $this->user,
        PaymentStatusChangedNotification::class,
        function ($notification) {
            $data = $notification->toArray($this->user);
            return $data['old_payment_status'] === 'pending'
                && $data['new_payment_status'] === 'paid';
        }
    );
});

test('notification to mail route is sent for guest bookings', function () {
    $guestBooking = Booking::factory()->create([
        'user_id' => null,
        'event_id' => $this->event->id,
        'customer_email' => 'guest@example.com',
        'status' => 'pending',
    ]);

    $guestBooking->status = 'confirmed';
    $guestBooking->save();

    Notification::assertSentOnDemand(BookingStatusChangedNotification::class);
});

test('booking status changed notification has correct mail content', function () {
    $notification = new BookingStatusChangedNotification($this->booking, 'pending', 'confirmed');
    $mail = $notification->toMail($this->user);

    expect($mail->subject)->toContain('Buchungsstatus geändert');
    expect($mail->subject)->toContain($this->booking->booking_number);
});

test('payment status changed notification has correct mail content for paid status', function () {
    $notification = new PaymentStatusChangedNotification($this->booking, 'pending', 'paid');
    $mail = $notification->toMail($this->user);

    expect($mail->subject)->toContain('Zahlungsstatus geändert');
    expect($mail->subject)->toContain($this->booking->booking_number);
});

test('payment status changed notification has correct mail content for failed status', function () {
    $notification = new PaymentStatusChangedNotification($this->booking, 'pending', 'failed');
    $mail = $notification->toMail($this->user);

    expect($mail->subject)->toContain('Zahlungsstatus geändert');
});

test('payment status changed notification has correct mail content for refunded status', function () {
    $notification = new PaymentStatusChangedNotification($this->booking, 'paid', 'refunded');
    $mail = $notification->toMail($this->user);

    expect($mail->subject)->toContain('Zahlungsstatus geändert');
});

test('multiple status changes send multiple notifications', function () {
    // First change
    $this->booking->status = 'confirmed';
    $this->booking->save();

    // Second change
    $this->booking->status = 'completed';
    $this->booking->save();

    Notification::assertSentTo(
        $this->user,
        BookingStatusChangedNotification::class,
        2
    );
});

test('simultaneous status and payment status changes send both notifications', function () {
    $this->booking->status = 'confirmed';
    $this->booking->payment_status = 'paid';
    $this->booking->save();

    Notification::assertSentTo($this->user, BookingStatusChangedNotification::class);
    Notification::assertSentTo($this->user, PaymentStatusChangedNotification::class);
});

test('notification array data includes booking number and url', function () {
    $this->booking->status = 'confirmed';
    $this->booking->save();

    Notification::assertSentTo(
        $this->user,
        BookingStatusChangedNotification::class,
        function ($notification) {
            $data = $notification->toArray($this->user);
            return isset($data['booking_number'])
                && isset($data['url'])
                && $data['booking_id'] === $this->booking->id;
        }
    );
});

