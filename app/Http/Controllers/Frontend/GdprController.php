<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\GdprService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GdprController extends Controller
{
    public function __construct(
        protected GdprService $gdprService
    ) {}

    public function privacy()
    {
        return view('frontend.gdpr.privacy');
    }

    public function exportRequest()
    {
        return view('frontend.dashboard.gdpr-export');
    }

    public function export(Request $request)
    {
        $client = Auth::guard('client')->user();

        $path = $this->gdprService->generateExportFile($client);

        return Storage::download($path, "mes-donnees-marrakechtours.json", [
            'Content-Type' => 'application/json',
        ]);
    }

    public function deleteRequest()
    {
        return view('frontend.dashboard.gdpr-delete');
    }

    public function delete(Request $request)
    {
        $client = Auth::guard('client')->user();

        $request->validate([
            'password' => 'required|current_password:client',
            'confirm_deletion' => 'required|accepted',
        ]);

        $keepBookings = $request->boolean('keep_bookings', true);

        $this->gdprService->deleteClientData($client, $keepBookings);

        Auth::guard('client')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', __('Votre compte a été supprimé conformément au RGPD.'));
    }
}
