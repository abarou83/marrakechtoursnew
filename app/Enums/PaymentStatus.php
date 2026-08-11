<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case PartiallyRefunded = 'partially_refunded';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('En attente'),
            self::Processing => __('En cours'),
            self::Paid => __('Payé'),
            self::Failed => __('Échoué'),
            self::Refunded => __('Remboursé'),
            self::PartiallyRefunded => __('Partiellement remboursé'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Processing => 'info',
            self::Paid => 'success',
            self::Failed => 'danger',
            self::Refunded => 'secondary',
            self::PartiallyRefunded => 'warning',
        };
    }

    public function isPaid(): bool
    {
        return in_array($this, [self::Paid, self::PartiallyRefunded]);
    }

    public function canRefund(): bool
    {
        return in_array($this, [self::Paid, self::PartiallyRefunded]);
    }
}
