<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $clients = Client::withCount('bookings')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.clients.index', compact('clients', 'search'));
    }

    public function show(Client $client)
    {
        $client->loadCount(['bookings', 'reviews']);
        $bookings = $client->bookings()
            ->with(['tour', 'tourDate', 'payment'])
            ->latest()
            ->paginate(10);

        return view('admin.clients.show', compact('client', 'bookings'));
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}
