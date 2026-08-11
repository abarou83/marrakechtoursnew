<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TourPromotion extends Model
{
    protected $fillable = [
        'tour_id',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
        'usage_limit',
        'used_count',
        'badge_text',
        'badge_color',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Vérifier si la promotion est active actuellement
     */
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculer le prix après réduction
     */
    public function calculateDiscountedPrice($originalPrice)
    {
        if ($this->discount_type === 'percentage') {
            return $originalPrice - ($originalPrice * $this->discount_value / 100);
        }
        
        return max(0, $originalPrice - $this->discount_value);
    }

    /**
     * Obtenir le texte de la réduction
     */
    public function getDiscountTextAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return '-' . number_format($this->discount_value, 0) . '%';
        }
        return '-' . number_format($this->discount_value, 2) . ' EUR';
    }

    /**
     * Scope pour obtenir les promotions actives
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                     ->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now)
                     ->where(function($q) {
                         $q->whereNull('usage_limit')
                           ->orWhereRaw('used_count < usage_limit');
                     });
    }
}




