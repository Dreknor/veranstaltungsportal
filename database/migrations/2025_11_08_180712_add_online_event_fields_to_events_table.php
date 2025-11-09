<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('event_type', ['physical', 'online', 'hybrid'])->default('physical')->after('event_category_id');
            $table->string('online_url')->nullable()->after('livestream_url');
            $table->string('online_access_code')->nullable()->after('online_url');

            // Mache Venue-Felder nullable für Online-Events
            $table->string('venue_name')->nullable()->change();
            $table->text('venue_address')->nullable()->change();
            $table->string('venue_city')->nullable()->change();
            $table->string('venue_postal_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_type', 'online_url', 'online_access_code']);

            // Setze Venue-Felder wieder auf required (nur wenn nötig)
            $table->string('venue_name')->nullable(false)->change();
            $table->text('venue_address')->nullable(false)->change();
            $table->string('venue_city')->nullable(false)->change();
            $table->string('venue_postal_code')->nullable(false)->change();
        });
    }
};
