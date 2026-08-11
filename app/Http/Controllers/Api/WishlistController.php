<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $wishlists = Wishlist::where('client_id', $client->id)
            ->with(['tour:id,slug'])
            ->get()
            ->pluck('tour.id');

        return response()->json([
            'success' => true,
            'tour_ids' => $wishlists,
        ]);
    }

    public function toggle(Request $request): JsonResponse
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return response()->json([
                'success' => false,
                'error' => 'login_required',
                'message' => __('Connectez-vous pour ajouter aux favoris'),
            ], 401);
        }

        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        $result = Wishlist::toggle($client->id, $request->tour_id);

        return response()->json([
            'success' => true,
            'added' => $result['added'],
            'message' => $result['message'],
            'count' => Wishlist::where('client_id', $client->id)->count(),
        ]);
    }

    public function remove(Request $request): JsonResponse
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'tour_id' => 'required|exists:tours,id',
        ]);

        Wishlist::where('client_id', $client->id)
            ->where('tour_id', $request->tour_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => __('Retiré des favoris'),
            'count' => Wishlist::where('client_id', $client->id)->count(),
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $client = Auth::guard('client')->user();

        if (!$client) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'tour_ids' => 'array',
            'tour_ids.*' => 'exists:tours,id',
        ]);

        $tourIds = $request->input('tour_ids', []);

        foreach ($tourIds as $tourId) {
            Wishlist::firstOrCreate([
                'client_id' => $client->id,
                'tour_id' => $tourId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Favoris synchronisés'),
            'count' => Wishlist::where('client_id', $client->id)->count(),
        ]);
    }
}
