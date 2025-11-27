<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop event_series table as it's been replaced by event_dates
     */
    public function up(): void
    {
        Schema::dropIfExists('event_series');
    }

    /**
     * Reverse the migrations.
     * Note: This is a destructive migration. Rollback is not recommended.
     */
    public function down(): void
    {
        // Not implementing rollback for deprecated table
        // If needed, restore from backup
    }
};

