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
        Schema::table('events', function (Blueprint $table) {
            // Duration in minutes
            $table->integer('duration')->nullable()->after('end_date');
        });

        // Update existing events with calculated duration
        DB::statement('UPDATE events SET duration = TIMESTAMPDIFF(MINUTE, start_date, end_date)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};

