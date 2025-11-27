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
            // Add flag to indicate if event has multiple dates
            if (!Schema::hasColumn('events', 'has_multiple_dates')) {
                $table->boolean('has_multiple_dates')->default(false)->after('end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'has_multiple_dates')) {
                $table->dropColumn('has_multiple_dates');
            }
        });
    }
};

