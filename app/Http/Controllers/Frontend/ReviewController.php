<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Enums\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(Booking $booking)
    {
        $client = Auth::guard('client')->user();

        if ($booking->client_id !== $client->id) {
            abort(403);
        }

        if ($booking->status !== BookingStatus::Completed) {
            return back()->with('error', __('Vous ne pouvez laisser un avis que pour une réservation terminée.'));
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
            return back()->with('error', __('Vous avez déjà laissé un avis pour cette réservation.'));
        }

        $booking->load(['tour.translations', 'tour.media']);

        return view('frontend.reviews.create', compact('booking'));
    }

    public function store(Request $request, Booking $booking)
    {
        $client = Auth::guard('client')->user();

        if ($booking->client_id !== $client->id) {
            abort(403);
        }

        if ($booking->status !== BookingStatus::Completed) {
            return back()->with('error', __('Vous ne pouvez laisser un avis que pour une réservation terminée.'));
        }

        if (Review::where('booking_id', $booking->id)->exists()) {
            return back()->with('error', __('Vous avez déjà laissé un avis pour cette réservation.'));
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:20|max:2000',
            'guide_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'recommend' => 'boolean',
        ]);

        $review = Review::create([
            'tour_id' => $booking->tour_id,
            'client_id' => $client->id,
            'booking_id' => $booking->id,
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'guide_rating' => $validated['guide_rating'] ?? null,
            'value_rating' => $validated['value_rating'] ?? null,
            'recommend' => $validated['recommend'] ?? true,
            'author_name' => $client->name,
            'author_country' => $client->country,
            'travel_date' => $booking->travel_date ?? $booking->booking_date,
            'travel_type' => $booking->pricing_mode,
            'is_verified' => true,
            'status' => 'pending',
            'locale' => app()->getLocale(),
        ]);

        return redirect()
            ->route('dashboard.reviews')
            ->with('success', __('Merci pour votre avis ! Il sera publié après vérification.'));
    }

    public function edit(Review $review)
    {
        $client = Auth::guard('client')->user();

        if ($review->client_id !== $client->id) {
            abort(403);
        }

        if ($review->status === 'approved') {
            return back()->with('error', __('Vous ne pouvez plus modifier un avis approuvé.'));
        }

        $review->load(['tour.translations', 'booking']);

        return view('frontend.reviews.edit', compact('review'));
    }

    public function update(Request $request, Review $review)
    {
        $client = Auth::guard('client')->user();

        if ($review->client_id !== $client->id) {
            abort(403);
        }

        if ($review->status === 'approved') {
            return back()->with('error', __('Vous ne pouvez plus modifier un avis approuvé.'));
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:20|max:2000',
            'guide_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'recommend' => 'boolean',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'title' => $validated['title'],
            'comment' => $validated['comment'],
            'guide_rating' => $validated['guide_rating'] ?? null,
            'value_rating' => $validated['value_rating'] ?? null,
            'recommend' => $validated['recommend'] ?? true,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('dashboard.reviews')
            ->with('success', __('Avis modifié avec succès.'));
    }

    public function destroy(Review $review)
    {
        $client = Auth::guard('client')->user();

        if ($review->client_id !== $client->id) {
            abort(403);
        }

        $review->delete();

        return back()->with('success', __('Avis supprimé.'));
    }
}
