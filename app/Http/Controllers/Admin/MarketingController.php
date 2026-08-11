<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MarketingService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MarketingController extends Controller
{
    public function __construct(
        protected MarketingService $marketingService
    ) {}

    public function index(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfDay();

        $stats = $this->marketingService->getDashboardStats($from, $to);

        return view('admin.marketing.index', compact('stats', 'from', 'to'));
    }
}
