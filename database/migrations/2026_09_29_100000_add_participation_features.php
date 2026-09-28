<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * - Tickets vor der (externen) Rechnungsstellung freigeben: pro Event (Standard) und pro Buchung (Ausnahme)
     * - Versand von Tickets/Zugangsdaten an einzelne Teilnehmende nachvollziehen
     * - Nachbereitung (Bescheinigung + Feedback) nach der Veranstaltung
     * - Wartelisten-Reservierung mit persönlichem Buchungslink
     * - Anwesenheit je Termin bei Veranstaltungen mit mehreren Terminen
     * - DSGVO-Anonymisierung
     * - Bewertungen auch ohne Benutzerkonto (Gastbuchungen)
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('tickets_before_invoice')->default(false)->after('free_ticket_auto_confirm');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('release_tickets_before_payment')->nullable()->after('payment_status');
            $table->timestamp('follow_up_sent_at')->nullable()->after('reminders_sent');
            $table->timestamp('anonymized_at')->nullable()->after('follow_up_sent_at');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->string('ticket_sent_to')->nullable()->after('attendee_organization');
            $table->timestamp('ticket_sent_at')->nullable()->after('ticket_sent_to');
        });

        Schema::table('event_waitlist', function (Blueprint $table) {
            $table->string('claim_token', 64)->nullable()->unique()->after('expires_at');
        });

        Schema::table('event_reviews', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        Schema::create('booking_item_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_date_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checked_in_at');
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['booking_item_id', 'event_date_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_item_attendances');

        Schema::table('event_waitlist', function (Blueprint $table) {
            $table->dropUnique(['claim_token']);
            $table->dropColumn('claim_token');
        });

        Schema::table('booking_items', function (Blueprint $table) {
            $table->dropColumn(['ticket_sent_to', 'ticket_sent_at']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['release_tickets_before_payment', 'follow_up_sent_at', 'anonymized_at']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('tickets_before_invoice');
        });
    }
};
