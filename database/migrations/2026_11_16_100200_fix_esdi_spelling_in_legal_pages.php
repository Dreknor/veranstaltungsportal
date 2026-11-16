<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Schreibweise der Firma in den Rechtstexten korrigieren: „ESDI GmbH“ → „ESdi GmbH“ (wie im Fuß).
 * Nur dieser exakte Ersatz; Platzhalter und übrige Inhalte bleiben unverändert.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('legal_pages')
            ->where('title', 'like', '%ESDI GmbH%')
            ->orWhere('content', 'like', '%ESDI GmbH%')
            ->get(['id', 'title', 'content'])
            ->each(function (object $page): void {
                DB::table('legal_pages')->where('id', $page->id)->update([
                    'title' => str_replace('ESDI GmbH', 'ESdi GmbH', (string) $page->title),
                    'content' => str_replace('ESDI GmbH', 'ESdi GmbH', (string) $page->content),
                ]);
            });
    }

    public function down(): void
    {
        // Die Korrektur wird nicht zurückgenommen.
    }
};
