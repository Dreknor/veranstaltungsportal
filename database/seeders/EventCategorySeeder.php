<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventCategorySeeder extends Seeder
{
    public function run(): void
    {

        $categories = [
            [
                'name' => 'Hauptfach Mensch',
                'slug' => 'hauptfach-mensch',
                'description' => 'Fortbildungen im Rahmen der Aktion Hauptfach Mensch - ganzheitliche Bildung',
                'icon' => 'heart',
                'color' => '#dc2626',
            ],
            [
                'name' => 'Pädagogik & Didaktik',
                'slug' => 'paedagogik-didaktik',
                'description' => 'Methoden, Unterrichtsgestaltung und pädagogische Konzepte',
                'icon' => 'academic-cap',
                'color' => '#2563eb',
            ],
            [
                'name' => 'Digitales Lehren & Lernen',
                'slug' => 'digitales-lehren-lernen',
                'description' => 'Digitale Tools, Medienbildung und E-Learning',
                'icon' => 'computer-desktop',
                'color' => '#7c3aed',
            ],
            [
                'name' => 'Schulentwicklung',
                'slug' => 'schulentwicklung',
                'description' => 'Schulkultur, Organisationsentwicklung und Qualitätsmanagement',
                'icon' => 'building-office',
                'color' => '#059669',
            ],
            [
                'name' => 'Seelsorge & Spiritualität',
                'slug' => 'seelsorge-spiritualitaet',
                'description' => 'Schulseelsorge, Andachten und spirituelle Begleitung',
                'icon' => 'sparkles',
                'color' => '#7c2d12',
            ],
            [
                'name' => 'Inklusion & Diversität',
                'slug' => 'inklusion-diversitaet',
                'description' => 'Inklusive Pädagogik, Umgang mit Vielfalt und Integration',
                'icon' => 'users',
                'color' => '#0891b2',
            ],
            [
                'name' => 'Fachfortbildungen',
                'slug' => 'fachfortbildungen',
                'description' => 'Fachspezifische Fortbildungen für einzelne Unterrichtsfächer',
                'icon' => 'book-open',
                'color' => '#ea580c',
            ],
            [
                'name' => 'Persönlichkeitsentwicklung',
                'slug' => 'persoenlichkeitsentwicklung',
                'description' => 'Selbstfürsorge, Resilienz und persönliche Kompetenzen',
                'icon' => 'user-circle',
                'color' => '#db2777',
            ],
            [
                'name' => 'Konfliktmanagement',
                'slug' => 'konfliktmanagement',
                'description' => 'Mediation, Gewaltprävention und Konfliktlösung',
                'icon' => 'shield-check',
                'color' => '#65a30d',
            ],
            [
                'name' => 'Elternarbeit & Kommunikation',
                'slug' => 'elternarbeit-kommunikation',
                'description' => 'Elterngespräche, Kooperation und Kommunikationsstrategien',
                'icon' => 'chat-bubble-left-right',
                'color' => '#0284c7',
            ],
            [
                'name' => 'Soziales Lernen',
                'slug' => 'soziales-lernen',
                'description' => 'Sozialkompetenzen, Klassengemeinschaft und Wertevermittlung',
                'icon' => 'user-group',
                'color' => '#16a34a',
            ],
            [
                'name' => 'Gesundheit & Bewegung',
                'slug' => 'gesundheit-bewegung',
                'description' => 'Gesundheitsförderung, Bewegungsangebote und Ergonomie',
                'icon' => 'heart-pulse',
                'color' => '#c026d3',
            ],
            [
                'name' => 'Rechtliche Grundlagen',
                'slug' => 'rechtliche-grundlagen',
                'description' => 'Schulrecht, Aufsichtspflicht und rechtliche Rahmenbedingungen',
                'icon' => 'scale',
                'color' => '#475569',
            ],
            [
                'name' => 'Netzwerktreffen',
                'slug' => 'netzwerktreffen',
                'description' => 'Kollegialer Austausch und schulübergreifende Zusammenarbeit',
                'icon' => 'link',
                'color' => '#0d9488',
            ],
            [
                'name' => 'Sonstiges',
                'slug' => 'sonstiges',
                'description' => 'Weitere Fortbildungen und Veranstaltungen',
                'icon' => 'ellipsis-horizontal',
                'color' => '#64748b',
            ],
        ];

        try {
            foreach ($categories as $category) {
                EventCategory::create($category);
            }
        } catch (\Exception $e) {

        }

    }
}

