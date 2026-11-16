<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Tippfehler im Label der Einstellung „Rechnungsnummer-Format für Veranstalter“ korrigieren. */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')
            ->where('label', 'Rechungsnummer-Format für Veranstalter')
            ->update(['label' => 'Rechnungsnummer-Format für Veranstalter']);
    }

    public function down(): void
    {
        // Die Korrektur wird nicht zurückgenommen.
    }
};
