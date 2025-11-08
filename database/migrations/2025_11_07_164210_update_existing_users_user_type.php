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
        // Update existing users: set user_type based on is_organizer field
        DB::table('users')
            ->where('is_organizer', true)
            ->update(['user_type' => 'organizer']);

        DB::table('users')
            ->where('is_organizer', false)
            ->update(['user_type' => 'participant']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse, user_type column will be dropped by the previous migration's rollback
    }
};
