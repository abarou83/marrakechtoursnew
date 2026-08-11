<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\BookingChannel;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Services\BookingService;
use App\Services\Channel\ManualChannelSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ChannelController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request)
    {
        $from = ($request->date('from') ?? now()->subDays(30))->copy()->startOfDay();
        $to = ($request->date('to') ?? now())->copy()->endOfDay();

        $byChannel = Booking::query()
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->whereIn('payment_status', [PaymentStatus::Paid, 'paid'])
            ->select('channel', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(total_ttc, total_price, 0)) as revenue'))
            ->groupBy('channel')
            ->orderByDesc('revenue')
            ->get();

        $otaBookings = Booking::with(['tour.translations'])
            ->whereIn('channel', [BookingChannel::Viator->value, BookingChannel::GetYourGuide->value, BookingChannel::WhatsApp->value, BookingChannel::Phone->value])
            ->when($request->filled('channel'), fn ($q) => $q->where('channel', $request->channel))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $channels = BookingChannel::options();

        return view('admin.channels.index', compact('byChannel', 'otaBookings', 'channels', 'from', 'to'));
    }

    public function create()
    {
        $tours = Tour::active()->with('translations')->get();
        $channels = collect(BookingChannel::cases())
            ->filter(fn (BookingChannel $c) => $c !== BookingChannel::Direct && $c !== BookingChannel::GiftCard)
            ->mapWithKeys(fn (BookingChannel $c) => [$c->value => $c->label()])
            ->all();

        return view('admin.channels.create', compact('tours', 'channels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'channel' => ['required', Rule::enum(BookingChannel::class)],
            'channel_external_id' => 'nullable|string|max:255',
            'channel_notes' => 'nullable|string|max:2000',
            'tour_id' => 'required|exists:tours,id',
            'travel_date' => 'required|date',
            'adults' => 'required|integer|min:1',
            'children' => 'integer|min:0',
            'infants' => 'integer|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'total_price' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid',
        ]);

        $channel = BookingChannel::from($validated['channel']);

        $booking = $this->bookingService->createBooking([
            ...$validated,
            'pricing_mode' => 'group',
            'status' => 'confirmed',
            'payment_status' => $validated['payment_status'],
        ]);

        $booking->update([
            'status' => BookingStatus::Confirmed,
            'payment_status' => PaymentStatus::from($validated['payment_status']),
            'channel' => $channel->value,
            'channel_external_id' => $validated['channel_external_id'] ?? null,
            'channel_notes' => $validated['channel_notes'] ?? null,
            'confirmed_at' => now(),
        ]);

        app(ManualChannelSync::class)->pushBooking($booking);

        return redirect()
            ->route('admin.channels.index')
            ->with('success', 'Réservation OTA enregistrée.');
    }
}
