<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'user_id',
        'tour_id',
        'name',
        'email',
        'phone',
        'message',
        'participants',
        'preferred_date',
        'estimated_budget',
        'status',
        'admin_notes',
        'viewed_at',
        'contacted_at',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'estimated_budget' => 'decimal:2',
        'viewed_at' => 'datetime',
        'contacted_at' => 'datetime',
        'participants' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeViewed($query)
    {
        return $query->where('status', 'viewed');
    }

    public function markAsViewed()
    {
        $this->update([
            'status' => 'viewed',
            'viewed_at' => now(),
        ]);
    }

    public function markAsContacted()
    {
        $this->update([
            'status' => 'contacted',
            'contacted_at' => now(),
        ]);
    }
}
