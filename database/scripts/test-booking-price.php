<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Tour;
use App\Services\PricingService;

$tour = Tour::where('slug', 'circuit-culturel-asie')->first();
$svc = app(PricingService::class);
$date = now()->addDays(3)->format('Y-m-d');

$result = $svc->calculatePrice($tour, 'group', $date, 2, 1, 0, [], null);
echo "Tour #{$tour->id} group total={$result['total_price']} pricing_id={$result['pricing_id']}\n";

$pricing = $tour->pricings()->where('id', $result['pricing_id'])->first();
echo 'Addons on pricing: '.$pricing->addons()->count()."\n";
