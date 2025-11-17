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
                'label' => 'Rechungsnummer-Format für Veranstalter',
                'description' => 'Format der Rechnungsnummer für Rechnungen an Kunden für Buchungen',
            ],
            [
                'key' => 'invoice_number_format_platform_fee',
                'value' => 'PF-{YEAR}-{NUMBER}',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
                'label' => 'Plattformgebühren Rechnungsnummer',
                'description' => 'Format der Rechnungsnummer für Rechnungen an Veranstalter für Plattformgebühren',

            ],
            [
                'key' => 'invoice_number_counter_booking',
                'value' => '1',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
                'label' => 'Rechnungsnummer Zähler für Buchungen',
                'description' => 'Startwert des Rechnungsnummernzählers für Rechnungen an Kunden für Buchungen',
            ],
            [
                'key' => 'invoice_number_counter_platform_fee',
                'value' => '1',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
                'label' => 'Rechnungsnummer Zähler für Plattformgebühren',
                'description' => 'Startwert des Rechnungsnummernzählers für Rechnungen an Veranstalter für Plattformgebühren',
            ],
            [
                'key' => 'invoice_number_padding',
                'value' => '5',
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
                'label' => 'Rechnungsnummer Auffüllung',
                'description' => 'Anzahl der Stellen, auf die die Rechnungsnummer aufgefüllt wird',
            ],
            [
                'key' => 'invoice_reset_yearly',
                'value' => 'true',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
                'label' => 'Jährlicher Rücksetzungsmodus für Rechnungsnummern',
                'description' => 'Ob die Rechnungsnummern jedes Jahr zurückgesetzt werden sollen',

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

