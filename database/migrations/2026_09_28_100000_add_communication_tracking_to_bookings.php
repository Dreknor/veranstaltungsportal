<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nachvollziehbare Kommunikation rund um Buchungen:
     * - bookings.reminders_sent: welche Erinnerungen (z. B. 24h, 3h) bereits versendet wurden
     * - bookings.cancellation_reason / cancelled_by: wer hat storniert und warum
     * - booking_email_logs: Verlauf aller E-Mails, die zu einer Buchung versendet wurden
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->json('reminders_sent')->nullable()->after('cancelled_at');
            $table->string('cancelled_by', 20)->nullable()->after('reminders_sent');
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');
        });

        Schema::create('booking_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('type', 100);
            $table->string('subject')->nullable();
            $table->string('recipient');
            $table->timestamp('sent_at')->useCurrent();

            $table->index(['booking_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_email_logs');

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reminders_sent', 'cancelled_by', 'cancellation_reason']);
        });
    }
};
