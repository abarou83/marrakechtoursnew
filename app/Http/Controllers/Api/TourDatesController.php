<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TourDatesController extends Controller
{
    /**
     * Get all departure times for a tour (independent of date)
     * 
     * GET /api/v1/tours/{tour}/times
     * 
     * @param Tour $tour
     * @return JsonResponse
     */
    public function getAllDepartureTimes(Tour $tour): JsonResponse
    {
        try {
            // Get ALL departure times for this tour (regardless of date)
            // Hours are independent of date, so we get all unique hours
            $tourDates = $tour->tourDates()
                ->orderBy('start_at')
                ->get();
            
            // Get all unique times (HH:mm) - hours are independent of date
            $uniqueTimes = [];
            foreach ($tourDates as $tourDate) {
                $timeKey = $tourDate->start_at->format('H:i');
                
                // Only add if we haven't seen this time yet
                if (!isset($uniqueTimes[$timeKey])) {
                    // For each unique time, get all tour_dates with this time (all dates)
                    $allDatesWithThisTime = $tour->tourDates()
                        ->whereTime('start_at', $timeKey)
                        ->get();
                    
                    // Calculate total capacity (sum of all capacities for this time across all dates)
                    $totalCapacity = $allDatesWithThisTime->sum('capacity');
                    
                    // Calculate total booked (sum of all bookings for this time across all dates)
                    $totalBooked = 0;
                    foreach ($allDatesWithThisTime as $td) {
                        $totalBooked += $td->bookings()
                            ->where('status', '!=', 'canceled')
                            ->sum('seats');
                    }
                    
                    $totalAvailable = max(0, $totalCapacity - $totalBooked);
                    
                    // Use the first tour_date id for this time
                    $uniqueTimes[$timeKey] = [
                        'id' => $tourDate->id,
                        'time' => $timeKey,
                        'capacity' => $totalCapacity,
                        'booked' => $totalBooked,
                        'available' => $totalAvailable,
                        'is_available' => $totalAvailable > 0,
                    ];
                }
            }
            
            // Sort by time (HH:mm)
            ksort($uniqueTimes);
            
            return response()->json([
                'success' => true,
                'times' => array_values($uniqueTimes)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading departure times',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get available departure times for a specific tour and date
     * 
     * GET /api/v1/tours/{tour}/dates/{date}/times
     * 
     * @param Tour $tour
     * @param string $date (YYYY-MM-DD)
     * @return JsonResponse
     */
    public function getDepartureTimes(Tour $tour, string $date): JsonResponse
    {
        try {
            $dateObj = Carbon::parse($date);
            
            // Get all tour dates for this specific date
            $tourDates = $tour->tourDates()
                ->whereDate('start_at', $dateObj->format('Y-m-d'))
                ->where('start_at', '>=', now()) // Only future dates
                ->orderBy('start_at')
                ->get();
            
            // Format response with time and availability
            $times = $tourDates->map(function($tourDate) {
                $booked = $tourDate->bookings()
                    ->where('status', '!=', 'canceled')
                    ->sum('seats');
                $available = $tourDate->capacity - $booked;
                
                return [
                    'id' => $tourDate->id,
                    'time' => $tourDate->start_at->format('H:i'),
                    'capacity' => $tourDate->capacity,
                    'booked' => $booked,
                    'available' => max(0, $available),
                    'is_available' => $available > 0,
                ];
            });
            
            return response()->json([
                'success' => true,
                'date' => $date,
                'times' => $times->values()->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}

