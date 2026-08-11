<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureUtmParameters
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasAny(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'])) {
            session([
                'utm' => array_filter([
                    'source' => $request->query('utm_source'),
                    'medium' => $request->query('utm_medium'),
                    'campaign' => $request->query('utm_campaign'),
                    'term' => $request->query('utm_term'),
                    'content' => $request->query('utm_content'),
                    'captured_at' => now()->toIso8601String(),
                ]),
            ]);
        }

        if ($request->has('ref')) {
            session(['referral_code' => strtoupper($request->query('ref'))]);
        }

        if ($request->has('channel')) {
            session(['booking_channel' => $request->query('channel')]);
        }

        return $next($request);
    }
}
