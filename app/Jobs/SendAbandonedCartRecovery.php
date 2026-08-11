<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\AbandonedCartMail;
use App\Models\AbandonedCart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAbandonedCartRecovery implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $delayHours = config('marketing.abandoned_cart.delay_hours', 2);
        $cutoff = now()->subHours($delayHours);

        $carts = AbandonedCart::with('tour.translations')
            ->pendingRecovery()
            ->where('marketing_opt_in', true)
            ->where('created_at', '<=', $cutoff)
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        foreach ($carts as $cart) {
            try {
                Mail::to($cart->email)->send(new AbandonedCartMail($cart));

                $cart->update(['recovery_email_sent_at' => now()]);

                Log::info('Abandoned cart recovery email sent', [
                    'cart_id' => $cart->id,
                    'email' => $cart->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send abandoned cart email', [
                    'cart_id' => $cart->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
