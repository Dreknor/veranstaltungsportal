<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group')->default('general')->after('name');
            $table->string('description')->nullable()->after('group');
            $table->boolean('is_system')->default(false)->after('description');
        });

        // Bestehende Permissions automatisch gruppieren (aus dem Namen extrahieren)
        // z.B. "view events" → group = "events"
        $permissions = \DB::table('permissions')->get();
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $group = count($parts) >= 2 ? $parts[1] : 'general';
            \DB::table('permissions')
                ->where('id', $permission->id)
                ->update(['group' => $group, 'is_system' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['group', 'description', 'is_system']);
        });
    }
};

