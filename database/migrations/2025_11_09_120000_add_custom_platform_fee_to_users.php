<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'custom_platform_fee')) {
                $table->json('custom_platform_fee')->nullable()->after('organizer_billing_data');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'custom_platform_fee')) {
                $table->dropColumn('custom_platform_fee');
            }
        });
    }
};

