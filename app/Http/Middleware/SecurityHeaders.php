<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(16));
        $request->attributes->set('csp-nonce', $nonce);
        view()->share('cspNonce', $nonce);
        Vite::useCspNonce($nonce);

        $response = $next($request);

        if (! $this->shouldAddHeaders($request, $response)) {
            return $response;
        }

        $this->addSecurityHeaders($response, $nonce);

        return $response;
    }

    private function shouldAddHeaders(Request $request, Response $response): bool
    {
        if ($request->is('livewire/*')) {
            return false;
        }

        $contentType = $response->headers->get('Content-Type', '');

        return str_contains($contentType, 'text/html') || empty($contentType);
    }

    private function addSecurityHeaders(Response $response, string $nonce): void
    {
        $csp = $this->buildContentSecurityPolicy($nonce);

        $response->headers->set('Content-Security-Policy', $csp);

        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        $response->headers->set('X-Frame-Options', 'DENY');

        $response->headers->set('X-Content-Type-Options', 'nosniff');

        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        $response->headers->set(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=(self), payment=(self)'
        );

        $response->headers->set('X-XSS-Protection', '1; mode=block');

        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
    }

    private function buildContentSecurityPolicy(string $nonce): string
    {
        $scriptSources = [
            "'self'",
            "'nonce-{$nonce}'",
            "'unsafe-eval'",
            'js.stripe.com',
            'cdn.jsdelivr.net',
            'cdnjs.cloudflare.com',
            'www.paypal.com',
            'www.google.com',
            'www.gstatic.com',
            'www.googletagmanager.com',
            'connect.facebook.net',
        ];

        $styleSources = [
            "'self'",
            "'unsafe-inline'",
            'fonts.googleapis.com',
            'fonts.bunny.net',
            'cdn.jsdelivr.net',
            'cdnjs.cloudflare.com',
        ];

        $fontSources = [
            "'self'",
            'data:',
            'fonts.gstatic.com',
            'fonts.bunny.net',
            'cdnjs.cloudflare.com',
        ];

        $policies = [
            "default-src 'self'",
            'script-src '.implode(' ', $scriptSources),
            'script-src-elem '.implode(' ', $scriptSources),
            'style-src '.implode(' ', $styleSources),
            'style-src-elem '.implode(' ', $styleSources),
            'font-src '.implode(' ', $fontSources),
            "img-src 'self' data: blob: cdn.jsdelivr.net *.stripe.com *.paypal.com maps.googleapis.com *.gstatic.com www.google.com *.googleusercontent.com *.tile.openstreetmap.org",
            'frame-src js.stripe.com www.paypal.com www.google.com',
            "connect-src 'self' cdn.jsdelivr.net api.stripe.com *.paypal.com www.google-analytics.com www.googletagmanager.com connect.facebook.net",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            'upgrade-insecure-requests',
        ];

        return implode('; ', $policies);
    }
}
