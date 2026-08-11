<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;
use App\Models\TourPricing;
use App\Models\TourAvailability;
use Illuminate\Support\Facades\DB;

$tours = Tour::withCount(['pricings', 'tourDates'])->get();

foreach ($tours as $t) {
    $gp = TourPricing::where('tour_id', $t->id)->where('is_active', 1)->count();
    $addons = DB::table('tour_addons')->where('tour_id', $t->id)->count();
    $pa = DB::table('pricing_addons')->whereIn(
        'tour_pricing_id',
        TourPricing::where('tour_id', $t->id)->pluck('id')
    )->count();
    $av = TourAvailability::where('tour_id', $t->id)
        ->where('is_available', 1)
        ->where('date', '>=', now()->toDateString())
        ->count();
    echo "#{$t->id} {$t->slug} pricings={$gp} tour_addons={$addons} pricing_addons={$pa} avail={$av} dates={$t->tour_dates_count}\n";
}
