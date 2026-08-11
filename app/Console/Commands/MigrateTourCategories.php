<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tour;
use Illuminate\Support\Facades\DB;

class MigrateTourCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tours:migrate-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing tour category_id to many-to-many relationship';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Migrating tour categories to many-to-many relationship...');

        $tours = Tour::whereNotNull('category_id')->get();
        $count = 0;

        foreach ($tours as $tour) {
            // Check if relationship already exists
            $exists = DB::table('category_tour')
                ->where('tour_id', $tour->id)
                ->where('category_id', $tour->category_id)
                ->exists();

            if (!$exists) {
                DB::table('category_tour')->insert([
                    'tour_id' => $tour->id,
                    'category_id' => $tour->category_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $count++;
            }
        }

        $this->info("Migrated {$count} tour categories successfully!");
        return 0;
    }
}
