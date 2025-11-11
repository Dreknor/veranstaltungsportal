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
        Schema::table('platform_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('platform_fees', 'minimum_fee')) {
                $table->decimal('minimum_fee', 10, 2)->default(1.00)->after('fee_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('platform_fees', function (Blueprint $table) {
            if (Schema::hasColumn('platform_fees', 'minimum_fee')) {
                $table->dropColumn('minimum_fee');
            }
        });
    }
};

