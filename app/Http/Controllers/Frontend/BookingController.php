<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Booking;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    public function __construct(
        protected SeoService $seoService
    ) {}

    public function create(Request $request, Tour $tour)
    {
        if (!$tour->is_active) {
            abort(404);
        }

        $tour->load(['pricings.groupPrices', 'pricings.privatePrices', 'pricings.addons', 'translations', 'media']);

        $seo = $this->seoService->generateMetaTags([
            'title' => __('Réserver :tour', ['tour' => $tour->translate()?->title ?? $tour->title]),
            'description' => __('Réservez votre excursion :tour à Marrakech', ['tour' => $tour->translate()?->title ?? $tour->title]),
            'type' => 'website',
            'robots' => 'noindex, nofollow',
        ]);

        return view('frontend.booking.create', compact('tour', 'seo'));
    }

    public function show(Booking $booking)
    {
        if (Auth::guard('client')->check()) {
            if (Auth::guard('client')->id() !== $booking->client_id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $booking->load(['tour.translations', 'tour.media', 'pricing', 'addons.addon']);

        return view('frontend.booking.show', compact('booking'));
    }

    public function confirmation(string $reference)
    {
        $booking = Booking::where('reference', $reference)
            ->with(['tour.translations', 'tour.media', 'pricing'])
            ->firstOrFail();

        $seo = $this->seoService->generateMetaTags([
            'title' => __('Confirmation de réservation :ref', ['ref' => $booking->reference]),
            'robots' => 'noindex, nofollow',
        ]);

        return view('frontend.booking.confirmation', compact('booking', 'seo'));
    }

    public function voucher(string $reference)
    {
        $booking = Booking::where('reference', $reference)
            ->with(['tour.translations', 'tour.media', 'pricing', 'addons.addon'])
            ->firstOrFail();

        if (!in_array($booking->status?->value ?? $booking->status, ['confirmed', 'completed'])) {
            abort(403, __('Le voucher n\'est disponible que pour les réservations confirmées.'));
        }

        $pdf = Pdf::loadView('pdf.voucher', compact('booking'));
        
        return $pdf->download("voucher-{$booking->reference}.pdf");
    }

    public function cancel(Request $request, Booking $booking)
    {
        if (Auth::guard('client')->check()) {
            if (Auth::guard('client')->id() !== $booking->client_id) {
                abort(403);
            }
        } else {
            abort(403);
        }

        if (!in_array($booking->status?->value ?? $booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', __('Cette réservation ne peut pas être annulée.'));
        }

        $cancellationDeadline = $booking->tour->booking_deadline_hours ?? 24;
        $travelDate = $booking->travel_date ?? $booking->booking_date;
        
        if ($travelDate && now()->diffInHours($travelDate, false) < $cancellationDeadline) {
            return back()->with('error', __('Vous ne pouvez plus annuler cette réservation.'));
        }

        return view('frontend.booking.cancel', compact('booking'));
    }
}
