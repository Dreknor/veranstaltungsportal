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
        Schema::table('event_series', function (Blueprint $table) {
            // Change recurrence_type to allow 'manual'
            // MySQL doesn't allow modifying ENUM easily, so we'll use raw SQL
        });

        // Update the column to allow 'manual' value
        DB::statement("ALTER TABLE event_series MODIFY COLUMN recurrence_type ENUM('daily', 'weekly', 'monthly', 'yearly', 'manual') NOT NULL DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values
        DB::statement("ALTER TABLE event_series MODIFY COLUMN recurrence_type ENUM('daily', 'weekly', 'monthly', 'yearly') NOT NULL");
    }
};
