<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTours = Tour::count();
        $totalBookings = Booking::count();
        $totalUsers = User::count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();

        $recentBookings = Booking::with(['tour', 'tourDate'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalTours',
            'totalBookings',
            'totalUsers',
            'totalRevenue',
            'pendingBookings',
            'confirmedBookings',
            'recentBookings'
        ));
    }
}
