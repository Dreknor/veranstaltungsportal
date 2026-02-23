<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->string('description')->nullable()->after('name');
            $table->string('color')->default('#6b7280')->after('description');
            $table->boolean('is_system')->default(false)->after('color');
        });

        // Bestehende System-Rollen markieren
        $systemRoles = ['admin', 'user', 'organizer', 'moderator', 'viewer'];
        \DB::table('roles')->whereIn('name', $systemRoles)->update(['is_system' => true]);
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'color', 'is_system']);
        });
    }
};

