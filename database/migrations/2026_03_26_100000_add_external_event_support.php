<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Extend event_type ENUM to include 'external' (MySQL only, SQLite ignores ENUM constraints)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE events MODIFY COLUMN event_type ENUM('physical', 'online', 'hybrid', 'external') NOT NULL DEFAULT 'physical'");
        }

        Schema::table('events', function (Blueprint $table) {
            $table->string('external_booking_url', 2048)->nullable()->after('online_access_code');
            $table->string('external_booking_button_text', 100)->nullable()->after('external_booking_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove external events first
        DB::table('events')->where('event_type', 'external')->update(['event_type' => 'physical']);

        // Revert ENUM (MySQL only)
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE events MODIFY COLUMN event_type ENUM('physical', 'online', 'hybrid') NOT NULL DEFAULT 'physical'");
        }

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['external_booking_url', 'external_booking_button_text']);
        });
    }
};

