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
        // Use database-agnostic approach for calculating duration
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('UPDATE events SET duration = TIMESTAMPDIFF(MINUTE, start_date, end_date)');
        } elseif ($driver === 'sqlite') {
            DB::statement("UPDATE events SET duration = CAST((julianday(end_date) - julianday(start_date)) * 24 * 60 AS INTEGER)");
        } elseif ($driver === 'pgsql') {
            DB::statement("UPDATE events SET duration = EXTRACT(EPOCH FROM (end_date - start_date)) / 60");
        }
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

