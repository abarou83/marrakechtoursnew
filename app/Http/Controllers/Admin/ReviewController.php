<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Tour;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Review::with(['tour', 'user']);

        // Filter by approval status
        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }

        // Search by tour name or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('tour', function($tourQuery) use ($search) {
                    $tourQuery->whereHas('translations', function($transQuery) use ($search) {
                        $transQuery->where('title', 'like', "%{$search}%");
                    });
                })
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->latest()->paginate(20);
        $tours = Tour::with('translations')->orderBy('id')->get();

        return view('admin.reviews.index', compact('reviews', 'tours'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tours = Tour::with('translations')->orderBy('id')->get();
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.reviews.create', compact('tours', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tour_id' => 'required|exists:tours,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_approved' => 'boolean',
        ]);

        Review::create($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Avis ajouté avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $review->load(['tour', 'user']);
        $tours = Tour::with('translations')->orderBy('id')->get();
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.reviews.edit', compact('review', 'tours', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tour_id' => 'required|exists:tours,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'is_approved' => 'boolean',
        ]);

        $review->update($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Avis modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Avis supprimé avec succès.');
    }

    /**
     * Approuver un avis
     */
    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Avis approuvé avec succès.');
    }

    /**
     * Rejeter un avis
     */
    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Avis rejeté avec succès.');
    }
}
