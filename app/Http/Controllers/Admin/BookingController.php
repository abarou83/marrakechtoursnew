<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Tour;
use App\Models\Client;
use App\Enums\BookingStatus;
use App\Enums\BookingChannel;
use App\Enums\PaymentStatus;
use App\Services\BookingService;
use App\Services\PaymentService;
use App\Services\AuditService;
use App\Events\BookingConfirmed;
use App\Events\BookingCancelled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected PaymentService $paymentService,
        protected AuditService $auditService
    ) {}

    public function index(Request $request)
    {
        $query = Booking::with(['tour.translations', 'client', 'pricing']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('travel_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('travel_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $bookings = $query->paginate(25)->withQueryString();

        $tours = Tour::active()->with('translations')->get();
        $statuses = BookingStatus::cases();
        $paymentStatuses = PaymentStatus::cases();
        $channels = BookingChannel::options();

        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::where('status', BookingStatus::Pending)->count(),
            'confirmed' => Booking::where('status', BookingStatus::Confirmed)->count(),
            'today_revenue' => Booking::where('status', BookingStatus::Confirmed)
                ->whereDate('created_at', today())
                ->sum('total_ttc'),
            'month_revenue' => Booking::where('status', BookingStatus::Confirmed)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_ttc'),
        ];

        return view('admin.bookings.index', compact(
            'bookings',
            'tours',
            'statuses',
            'paymentStatuses',
            'channels',
            'stats'
        ));
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'tour.translations',
            'tour.media',
            'client',
            'pricing.translations',
            'addons.addon.translations',
            'promoCode',
        ]);

        return view('admin.bookings.show', compact('booking'))->with([
            'channels' => BookingChannel::options(),
        ]);
    }

    public function create()
    {
        $tours = Tour::active()
            ->with(['translations', 'pricings.translations'])
            ->get();

        $clients = Client::orderBy('name')->get();

        return view('admin.bookings.create', compact('tours', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tour_id' => 'required|exists:tours,id',
            'client_id' => 'nullable|exists:clients,id',
            'pricing_id' => 'nullable|exists:tour_pricings,id',
            'pricing_mode' => 'required|in:group,private',
            'travel_date' => 'required|date|after:today',
            'adults' => 'required|integer|min:1',
            'children' => 'integer|min:0',
            'infants' => 'integer|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'special_requests' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed',
            'payment_status' => 'required|in:pending,paid',
            'total_price' => 'required|numeric|min:0',
        ]);

        try {
            $booking = $this->bookingService->createBooking($validated);

            if ($validated['status'] === 'confirmed') {
                $booking->update([
                    'status' => BookingStatus::Confirmed,
                    'confirmed_at' => now(),
                ]);
                event(new BookingConfirmed($booking));
            }

            $this->auditService->logCreate($booking);

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', 'Réservation créée avec succès.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function edit(Booking $booking)
    {
        $booking->load(['tour.translations', 'pricing', 'addons']);

        $tours = Tour::active()
            ->with(['translations', 'pricings.translations'])
            ->get();

        $clients = Client::orderBy('name')->get();

        return view('admin.bookings.edit', compact('booking', 'tours', 'clients'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'travel_date' => 'required|date',
            'adults' => 'required|integer|min:1',
            'children' => 'integer|min:0',
            'infants' => 'integer|min:0',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $oldValues = $booking->getOriginal();
        $booking->update($validated);

        $this->auditService->logUpdate($booking, $oldValues);

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', 'Réservation mise à jour.');
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $oldStatus = $booking->status;
        $newStatus = BookingStatus::from($request->status);

        DB::transaction(function () use ($booking, $newStatus, $oldStatus) {
            $booking->update(['status' => $newStatus]);

            if ($newStatus === BookingStatus::Confirmed && $oldStatus !== BookingStatus::Confirmed) {
                $booking->update(['confirmed_at' => now()]);
                event(new BookingConfirmed($booking));
            }

            if ($newStatus === BookingStatus::Cancelled && $oldStatus !== BookingStatus::Cancelled) {
                $booking->update([
                    'cancelled_at' => now(),
                    'cancellation_reason' => request('cancellation_reason', 'Annulé par admin'),
                ]);
                
                app(\App\Services\AvailabilityService::class)->releaseSpots(
                    $booking->tour,
                    $booking->travel_date ?? $booking->booking_date,
                    $booking->adults + $booking->children + $booking->infants
                );

                event(new BookingCancelled($booking));
            }
        });

        $this->auditService->logStatusChange(
            $booking,
            $oldStatus->value ?? (string) $oldStatus,
            $newStatus->value
        );

        return back()->with('success', 'Statut mis à jour.');
    }

    public function updateChannel(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'channel' => ['required', 'in:' . implode(',', array_column(BookingChannel::cases(), 'value'))],
            'channel_external_id' => 'nullable|string|max:255',
            'channel_notes' => 'nullable|string|max:2000',
        ]);

        $oldValues = $booking->only(['channel', 'channel_external_id', 'channel_notes']);
        $booking->update($validated);

        $this->auditService->logUpdate($booking, $oldValues);

        return back()->with('success', 'Canal mis à jour.');
    }

    public function refund(Request $request, Booking $booking)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0|max:' . ($booking->total_ttc ?? $booking->total_price),
            'reason' => 'nullable|string|max:500',
        ]);

        $amount = $request->amount ?? ($booking->total_ttc ?? $booking->total_price);

        try {
            $this->paymentService->refund(
                $booking->payment_intent_id,
                $amount,
                $request->reason
            );

            $booking->update([
                'payment_status' => PaymentStatus::Refunded,
                'refund_amount' => $amount,
                'refunded_at' => now(),
            ]);

            $this->auditService->logRefund($booking, (float) $amount);

            return back()->with('success', "Remboursement de {$amount}€ effectué.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur remboursement: ' . $e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        if (in_array($booking->status, [BookingStatus::Confirmed, BookingStatus::Completed])) {
            return back()->with('error', 'Impossible de supprimer une réservation confirmée ou terminée.');
        }

        $booking->delete();

        $this->auditService->logDelete($booking);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Réservation supprimée.');
    }

    public function export(Request $request)
    {
        $query = Booking::with(['tour.translations', 'client']);

        if ($request->filled('date_from')) {
            $query->whereDate('travel_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('travel_date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        $this->auditService->logExport('bookings', $bookings->count());

        $filename = 'bookings-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Référence',
                'Tour',
                'Date',
                'Client',
                'Email',
                'Téléphone',
                'Adultes',
                'Enfants',
                'Total',
                'Statut',
                'Paiement',
                'Créé le',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->reference,
                    $booking->tour->translate()?->title ?? $booking->tour->title,
                    $booking->travel_date?->format('d/m/Y'),
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_phone,
                    $booking->adults,
                    $booking->children,
                    number_format($booking->total_ttc ?? $booking->total_price, 2) . '€',
                    $booking->status->value ?? $booking->status,
                    $booking->payment_status->value ?? $booking->payment_status,
                    $booking->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
