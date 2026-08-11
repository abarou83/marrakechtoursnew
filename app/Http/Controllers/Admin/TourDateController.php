<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourDate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TourDateController extends Controller
{
    public function index(Tour $tour)
    {
        $tourDates = $tour->tourDates()->latest()->paginate(20);
        return view('admin.tour-dates.index', compact('tour', 'tourDates'));
    }

    public function create(Tour $tour)
    {
        return view('admin.tour-dates.create', compact('tour'));
    }

    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'departure_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'capacity' => 'required|integer|min:1',
        ]);

        // Utiliser automatiquement la date d'aujourd'hui
        $date = Carbon::today()->format('Y-m-d');

        // Combiner la date d'aujourd'hui et l'heure de départ
        $startAt = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $validated['departure_time']);
        
        // Si l'heure de fin est fournie, l'utiliser, sinon utiliser la même date avec l'heure de départ + 2 heures par défaut
        if (!empty($validated['end_time'])) {
            $endAt = Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $validated['end_time']);
        } else {
            $endAt = $startAt->copy()->addHours(2); // Par défaut, 2 heures après le départ
        }

        $tour->tourDates()->create([
            'start_at' => $startAt,
            'end_at' => $endAt,
            'capacity' => $validated['capacity'],
        ]);

        return redirect()->to(route('admin.tours.edit', $tour) . '?tab=dates')
            ->with('success', 'Heure de départ ajoutée avec succès.');
    }

    public function edit(Tour $tour, TourDate $tourDate)
    {
        return view('admin.tour-dates.edit', compact('tour', 'tourDate'));
    }

    public function update(Request $request, Tour $tour, TourDate $tourDate)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'departure_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'capacity' => 'required|integer|min:1',
        ]);

        // Combiner la date et l'heure de départ
        $startAt = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['departure_time']);
        
        // Si l'heure de fin est fournie, l'utiliser, sinon utiliser la même date avec l'heure de départ + 2 heures par défaut
        if (!empty($validated['end_time'])) {
            $endAt = Carbon::createFromFormat('Y-m-d H:i', $validated['date'] . ' ' . $validated['end_time']);
        } else {
            $endAt = $startAt->copy()->addHours(2); // Par défaut, 2 heures après le départ
        }

        $tourDate->update([
            'start_at' => $startAt,
            'end_at' => $endAt,
            'capacity' => $validated['capacity'],
        ]);

        return redirect()->route('admin.tour-dates.index', $tour)
            ->with('success', 'Date mise à jour avec succès.');
    }

    public function destroy(Tour $tour, TourDate $tourDate)
    {
        $tourDate->delete();

        return redirect()->to(route('admin.tours.edit', $tour) . '?tab=dates')
            ->with('success', 'Date supprimée avec succès.');
    }
}
