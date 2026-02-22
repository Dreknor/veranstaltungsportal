<?php

namespace App\Console\Commands;

use App\Models\EventCategory;
use Illuminate\Console\Command;

class FixCategoryIcons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-category-icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setzt Kategorie-Icons auf Heroicon-Komponentennamen zurück';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Map von FA-Klassen und alten Namen -> Heroicon-Komponentenname
        $map = [
            // FA-Klassen -> Heroicon
            'fas fa-heart'          => 'heart',
            'fas fa-graduation-cap' => 'academic-cap',
            'fas fa-laptop'         => 'computer-desktop',
            'fas fa-school'         => 'building-office',
            'fas fa-star'           => 'sparkles',
            'fas fa-users'          => 'users',
            'fas fa-book-open'      => 'book-open',
            'fas fa-user'           => 'user-circle',
            'fas fa-shield-halved'  => 'shield-check',
            'fas fa-comments'       => 'chat-bubble-left-right',
            'fas fa-people-group'   => 'user-group',
            'fas fa-heartbeat'      => 'heart-pulse',
            'fas fa-scale-balanced' => 'scale',
            'fas fa-link'           => 'link',
            'fas fa-ellipsis'       => 'ellipsis-horizontal',
        ];

        foreach ($map as $old => $new) {
            $count = EventCategory::where('icon', $old)->update(['icon' => $new]);
            if ($count > 0) {
                $this->line("{$old} -> {$new} ({$count} updated)");
            }
        }

        $this->info('Done!');
    }
}
