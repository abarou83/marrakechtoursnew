<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\Wishlist;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();

        $upcomingBookings = Booking::where('client_id', $client->id)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::Pending])
            ->where(function ($q) {
                $q->whereDate('travel_date', '>=', today())
                  ->orWhereDate('booking_date', '>=', today());
            })
            ->with(['tour.translations', 'tour.media'])
            ->orderBy('travel_date')
            ->take(3)
            ->get();

        $stats = [
            'total_bookings' => Booking::where('client_id', $client->id)->count(),
            'upcoming' => $upcomingBookings->count(),
            'completed' => Booking::where('client_id', $client->id)
                ->where('status', BookingStatus::Completed)->count(),
            'reviews' => Review::where('client_id', $client->id)->count(),
            'wishlist' => Wishlist::where('client_id', $client->id)->count(),
        ];

        return view('frontend.dashboard.index', compact('client', 'upcomingBookings', 'stats'));
    }

    public function bookings(Request $request)
    {
        $client = Auth::guard('client')->user();

        $query = Booking::where('client_id', $client->id)
            ->with(['tour.translations', 'tour.media', 'pricing']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('created_at')->paginate(10);

        return view('frontend.dashboard.bookings', compact('bookings'));
    }

    public function bookingShow(Booking $booking)
    {
        $client = Auth::guard('client')->user();

        if ($booking->client_id !== $client->id) {
            abort(403);
        }

        $booking->load(['tour.translations', 'tour.media', 'pricing', 'addons.addon', 'promoCode']);

        $canReview = $booking->status === BookingStatus::Completed
            && !Review::where('booking_id', $booking->id)->exists();

        return view('frontend.dashboard.booking-show', compact('booking', 'canReview'));
    }

    public function profile()
    {
        $client = Auth::guard('client')->user();

        return view('frontend.dashboard.profile', compact('client'));
    }

    public function updateProfile(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'preferred_language' => 'nullable|string|in:fr,en,es,ar',
            'preferred_currency' => 'nullable|string|in:EUR,USD,GBP,MAD',
        ]);

        $client->update($validated);

        return back()->with('success', __('Profil mis à jour avec succès.'));
    }

    public function updatePassword(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'current_password' => 'required|current_password:client',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $client->update([
            'password' => bcrypt($request->password),
        ]);

        return back()->with('success', __('Mot de passe modifié avec succès.'));
    }

    public function wishlist()
    {
        $client = Auth::guard('client')->user();

        $wishlists = Wishlist::where('client_id', $client->id)
            ->with(['tour.translations', 'tour.media', 'tour.pricings.groupPrices'])
            ->latest()
            ->paginate(12);

        return view('frontend.dashboard.wishlist', compact('wishlists'));
    }

    public function reviews()
    {
        $client = Auth::guard('client')->user();

        $reviews = Review::where('client_id', $client->id)
            ->with(['tour.translations', 'tour.media', 'booking'])
            ->latest()
            ->paginate(10);

        $pendingReviews = Booking::where('client_id', $client->id)
            ->where('status', BookingStatus::Completed)
            ->whereDoesntHave('review')
            ->with(['tour.translations'])
            ->get();

        return view('frontend.dashboard.reviews', compact('reviews', 'pendingReviews'));
    }

    public function notifications()
    {
        $client = Auth::guard('client')->user();

        return view('frontend.dashboard.notifications', compact('client'));
    }

    public function updateNotifications(Request $request)
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'email_booking_confirmation' => 'boolean',
            'email_booking_reminder' => 'boolean',
            'email_promotions' => 'boolean',
            'email_newsletter' => 'boolean',
        ]);

        $client->update([
            'notification_preferences' => $validated,
        ]);

        return back()->with('success', __('Préférences de notification mises à jour.'));
    }

    public function referral()
    {
        $client = Auth::guard('client')->user();
        $stats = app(\App\Services\ReferralService::class)->getStatsForClient($client);

        return view('frontend.dashboard.referral', compact('client', 'stats'));
    }
}
