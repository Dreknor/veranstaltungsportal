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
        // Insert default invoice number settings
        DB::table('settings')->insert([
            [
                'key' => 'invoice_number_format_booking',
                'value' => 'RE-{YEAR}-{NUMBER}',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'invoice_number_format_platform_fee',
                'value' => 'PF-{YEAR}-{NUMBER}',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'invoice_number_counter_booking',
                'value' => '1',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'invoice_number_counter_platform_fee',
                'value' => '1',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'invoice_number_padding',
                'value' => '5',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'invoice_reset_yearly',
                'value' => 'true',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'invoice_number_format_booking',
            'invoice_number_format_platform_fee',
            'invoice_number_counter_booking',
            'invoice_number_counter_platform_fee',
            'invoice_number_padding',
            'invoice_reset_yearly',
        ])->delete();
    }
};

