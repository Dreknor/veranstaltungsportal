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
            // Add series support
            $table->foreignId('series_id')->nullable()->after('user_id')->constrained('event_series')->onDelete('cascade');
            $table->integer('series_position')->nullable()->after('series_id');
            $table->boolean('is_series_part')->default(false)->after('series_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['series_id']);
            $table->dropColumn(['series_id', 'series_position', 'is_series_part']);
        });
    }
};

