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
            $table->string('billing_company')->nullable()->after('organization_description');
            $table->string('billing_address')->nullable()->after('billing_company');
            $table->string('billing_address_line2')->nullable()->after('billing_address');
            $table->string('billing_postal_code')->nullable()->after('billing_address_line2');
            $table->string('billing_city')->nullable()->after('billing_postal_code');
            $table->string('billing_state')->nullable()->after('billing_city');
            $table->string('billing_country')->default('Deutschland')->after('billing_state');
            $table->string('tax_id')->nullable()->after('billing_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'billing_company',
                'billing_address',
                'billing_address_line2',
                'billing_postal_code',
                'billing_city',
                'billing_state',
                'billing_country',
                'tax_id',
            ]);
        });
    }
};

