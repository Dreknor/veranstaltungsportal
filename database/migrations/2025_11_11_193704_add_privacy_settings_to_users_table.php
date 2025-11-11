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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('allow_connections')->default(true)->after('bio');
            $table->boolean('show_profile_publicly')->default(true)->after('allow_connections');
            $table->boolean('show_email_to_connections')->default(false)->after('show_profile_publicly');
            $table->boolean('show_phone_to_connections')->default(false)->after('show_email_to_connections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'allow_connections',
                'show_profile_publicly',
                'show_email_to_connections',
                'show_phone_to_connections',
            ]);
        });
    }
};

