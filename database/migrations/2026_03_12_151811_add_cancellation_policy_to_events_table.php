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
            $table->boolean('cancellation_allowed')->default(false)->after('requires_ticket');
            $table->unsignedSmallInteger('cancellation_days_before')->nullable()->after('cancellation_allowed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['cancellation_allowed', 'cancellation_days_before']);
        });
    }
};
