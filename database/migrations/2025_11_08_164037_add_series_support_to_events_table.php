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
            // Only add columns if they don't exist
            if (!Schema::hasColumn('events', 'series_position')) {
                $table->integer('series_position')->nullable()->after('series_id');
            }

            if (!Schema::hasColumn('events', 'is_series_part')) {
                $table->boolean('is_series_part')->default(false)->after('series_position');
            }
        });

        // Add foreign key constraint separately to avoid issues
        if (Schema::hasColumn('events', 'series_id') && Schema::hasTable('event_series')) {
            try {
                Schema::table('events', function (Blueprint $table) {
                    $table->foreign('series_id')
                        ->references('id')
                        ->on('event_series')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Try to drop foreign key
            try {
                $table->dropForeign(['series_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, ignore
            }

            // Drop columns if they exist
            if (Schema::hasColumn('events', 'series_position')) {
                $table->dropColumn('series_position');
            }
            if (Schema::hasColumn('events', 'is_series_part')) {
                $table->dropColumn('is_series_part');
            }
        });
    }
};

