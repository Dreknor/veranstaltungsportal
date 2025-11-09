<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all NULL notification_preferences to empty JSON object
        DB::table('users')
            ->whereNull('notification_preferences')
            ->update(['notification_preferences' => json_encode([
                'booking_notifications' => true,
                'event_updates' => true,
                'reminder_notifications' => true,
            ])]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optional: Reset to NULL if needed
        DB::table('users')->update(['notification_preferences' => null]);
    }
};
