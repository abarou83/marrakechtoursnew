<?php

declare(strict_types=1);

namespace App\Enums;

enum BookingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case Refunded = 'refunded';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('En attente'),
            self::Confirmed => __('Confirmée'),
            self::Cancelled => __('Annulée'),
            self::Completed => __('Terminée'),
            self::Refunded => __('Remboursée'),
            self::NoShow => __('Non présenté'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Confirmed => 'success',
            self::Cancelled => 'danger',
            self::Completed => 'secondary',
            self::Refunded => 'info',
            self::NoShow => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Pending => 'clock',
            self::Confirmed => 'check-circle',
            self::Cancelled => 'x-circle',
            self::Completed => 'flag',
            self::Refunded => 'arrow-uturn-left',
            self::NoShow => 'user-minus',
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::Pending => in_array($newStatus, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($newStatus, [self::Completed, self::Cancelled, self::NoShow]),
            self::Cancelled => $newStatus === self::Refunded,
            self::Completed, self::Refunded, self::NoShow => false,
        };
    }
}
