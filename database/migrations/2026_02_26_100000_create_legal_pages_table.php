<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_pages', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique(); // impressum, datenschutz, agb
            $table->string('title');
            $table->longText('content');
            $table->timestamp('last_updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });

        // Default-Einträge anlegen
        DB::table('legal_pages')->insert([
            [
                'type'            => 'impressum',
                'title'           => 'Impressum',
                'content'         => '<p><strong>ESDI GmbH</strong><br>[Straße und Hausnummer]<br>[PLZ Ort]<br>Deutschland</p><p>Telefon: [Telefonnummer]<br>E-Mail: info@esdigmbh.de</p><p>Registergericht: [Amtsgericht]<br>Registernummer: [HRB-Nummer]</p><p>Vertretungsberechtigte Geschäftsführung: [Name der Geschäftsführer]</p><p>USt-IdNr.: [DE-Nummer gemäß §27a UStG]</p>',
                'last_updated_at' => now(),
                'updated_by'      => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'type'            => 'datenschutz',
                'title'           => 'Datenschutzerklärung',
                'content'         => '<h2>1. Verantwortlicher</h2><p>Verantwortlich für die Datenverarbeitung auf dieser Website ist:<br><strong>ESDI GmbH</strong><br>[Straße und Hausnummer]<br>[PLZ Ort]<br>E-Mail: datenschutz@esdigmbh.de</p><h2>2. Ihre Rechte</h2><p>Sie haben das Recht auf Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung, Datenübertragbarkeit und Widerspruch.</p>',
                'last_updated_at' => now(),
                'updated_by'      => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'type'            => 'agb',
                'title'           => 'Allgemeine Geschäftsbedingungen (AGB)',
                'content'         => '<h2>§ 1 Geltungsbereich</h2><p>Diese Allgemeinen Geschäftsbedingungen gelten für die Nutzung der Plattform der ESDI GmbH.</p><h2>§ 2 Vertragsschluss</h2><p>Mit der Registrierung auf der Plattform kommt ein Nutzungsvertrag zustande.</p><h2>§ 3 Haftung</h2><p>Die Haftung der ESDI GmbH ist auf Vorsatz und grobe Fahrlässigkeit beschränkt.</p>',
                'last_updated_at' => now(),
                'updated_by'      => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_pages');
    }
};

