<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\AbandonedCart;
use App\Models\Booking;
use App\Models\NewsletterSubscriber;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketingService
{
    public function getDashboardStats(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        $bookingsQuery = Booking::query()
            ->where('payment_status', PaymentStatus::Paid)
            ->whereBetween('created_at', [$from, $to]);

        $revenue = (clone $bookingsQuery)->sum('total_ttc');
        $bookingCount = (clone $bookingsQuery)->count();

        $byChannel = Booking::query()
            ->select('channel', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_ttc) as revenue'))
            ->where('payment_status', PaymentStatus::Paid)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('channel')
            ->orderByDesc('revenue')
            ->get();

        $byUtmSource = Booking::query()
            ->select('utm_source', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_ttc) as revenue'))
            ->where('payment_status', PaymentStatus::Paid)
            ->whereNotNull('utm_source')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('utm_source')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        $byLocale = Booking::query()
            ->select('locale', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_ttc) as revenue'))
            ->where('payment_status', PaymentStatus::Paid)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('locale')
            ->orderByDesc('revenue')
            ->get();

        $conversionRate = $this->calculateConversionRate($from, $to);

        $newsletterCount = NewsletterSubscriber::whereNull('unsubscribed_at')->count();
        $abandonedCarts = AbandonedCart::whereNull('converted_at')->count();
        $recoveredCarts = AbandonedCart::whereNotNull('converted_at')
            ->whereBetween('converted_at', [$from, $to])
            ->count();

        $referralCount = DB::table('referral_usages')
            ->whereBetween('created_at', [$from, $to])
            ->count();

        $dailyRevenue = Booking::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_ttc) as revenue'), DB::raw('COUNT(*) as bookings'))
            ->where('payment_status', PaymentStatus::Paid)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            'revenue' => $revenue,
            'booking_count' => $bookingCount,
            'average_order' => $bookingCount > 0 ? round($revenue / $bookingCount, 2) : 0,
            'conversion_rate' => $conversionRate,
            'by_channel' => $byChannel,
            'by_utm_source' => $byUtmSource,
            'by_locale' => $byLocale,
            'daily_revenue' => $dailyRevenue,
            'newsletter_subscribers' => $newsletterCount,
            'abandoned_carts' => $abandonedCarts,
            'recovered_carts' => $recoveredCarts,
            'referral_count' => $referralCount,
        ];
    }

    protected function calculateConversionRate(Carbon $from, Carbon $to): float
    {
        $started = AbandonedCart::whereBetween('created_at', [$from, $to])->count()
            + Booking::whereBetween('created_at', [$from, $to])->count();

        $completed = Booking::where('payment_status', PaymentStatus::Paid)
            ->whereBetween('created_at', [$from, $to])
            ->count();

        if ($started === 0) {
            return 0;
        }

        return round(($completed / $started) * 100, 1);
    }
}
