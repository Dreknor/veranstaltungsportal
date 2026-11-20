<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bewertungen sichtbar machen:
 * - events.predecessor_id verknüpft eine Veranstaltung mit ihrem früheren Termin (wiederkehrendes Angebot),
 * - event_reviews bekommen Antwort des Veranstalters, Ablehnungszeitpunkt und Namensanzeige.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('predecessor_id')->nullable()->after('organization_id')
                ->constrained('events')->nullOnDelete();
        });

        Schema::table('event_reviews', function (Blueprint $table) {
            $table->boolean('show_full_name')->default(false)->after('comment');
            $table->text('organizer_reply')->nullable()->after('is_approved');
            $table->timestamp('organizer_replied_at')->nullable()->after('organizer_reply');
            $table->timestamp('rejected_at')->nullable()->after('organizer_replied_at');
        });
    }

    public function down(): void
    {
        Schema::table('event_reviews', function (Blueprint $table) {
            $table->dropColumn(['show_full_name', 'organizer_reply', 'organizer_replied_at', 'rejected_at']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('predecessor_id');
        });
    }
};
