<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Tour;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Quote::with(['tour', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by name, email or message
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $quotes = $query->latest()->paginate(20);
        $statuses = ['pending', 'viewed', 'contacted', 'accepted', 'rejected'];

        return view('admin.quotes.index', compact('quotes', 'statuses'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote)
    {
        $quote->load(['tour', 'user']);
        
        // Marquer comme vue si c'est la première fois
        if ($quote->status === 'pending') {
            $quote->markAsViewed();
        }

        return view('admin.quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote)
    {
        $quote->load(['tour', 'user']);
        $tours = Tour::with('translations')->orderBy('id')->get();
        $statuses = ['pending', 'viewed', 'contacted', 'accepted', 'rejected'];

        return view('admin.quotes.edit', compact('quote', 'tours', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quote $quote)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,viewed,contacted,accepted,rejected',
            'tour_id' => 'nullable|exists:tours,id',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? null,
        ];

        if (isset($data['tour_id'])) {
            $updateData['tour_id'] = $data['tour_id'];
        }

        // Marquer les dates automatiquement selon le statut
        if ($data['status'] === 'viewed' && !$quote->viewed_at) {
            $updateData['viewed_at'] = now();
        }

        if ($data['status'] === 'contacted' && !$quote->contacted_at) {
            $updateData['contacted_at'] = now();
        }

        $quote->update($updateData);

        return redirect()->route('admin.quotes.show', $quote)
            ->with('success', 'Devis mis à jour avec succès.');
    }

    /**
     * Update the status of a quote.
     */
    public function updateStatus(Request $request, Quote $quote)
    {
        $request->validate([
            'status' => 'required|in:pending,viewed,contacted,accepted,rejected',
        ]);

        $updateData = ['status' => $request->status];

        // Marquer les dates automatiquement selon le statut
        if ($request->status === 'viewed' && !$quote->viewed_at) {
            $updateData['viewed_at'] = now();
        }

        if ($request->status === 'contacted' && !$quote->contacted_at) {
            $updateData['contacted_at'] = now();
        }

        $quote->update($updateData);

        return redirect()->route('admin.quotes.index')
            ->with('success', 'Statut du devis mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quote $quote)
    {
        $quote->delete();

        return redirect()->route('admin.quotes.index')
            ->with('success', 'Devis supprimé avec succès.');
    }
}
