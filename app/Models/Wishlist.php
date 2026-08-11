<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = [
        'client_id',
        'tour_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }

    public static function toggle(int $clientId, int $tourId): array
    {
        $existing = static::where('client_id', $clientId)
            ->where('tour_id', $tourId)
            ->first();

        if ($existing) {
            $existing->delete();
            return ['added' => false, 'message' => __('Retiré des favoris')];
        }

        static::create([
            'client_id' => $clientId,
            'tour_id' => $tourId,
        ]);

        return ['added' => true, 'message' => __('Ajouté aux favoris')];
    }

    public static function isInWishlist(int $clientId, int $tourId): bool
    {
        return static::where('client_id', $clientId)
            ->where('tour_id', $tourId)
            ->exists();
    }
}
