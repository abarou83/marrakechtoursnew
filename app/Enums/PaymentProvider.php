<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentProvider: string
{
    case Stripe = 'stripe';
    case PayPal = 'paypal';
    case BankTransfer = 'bank_transfer';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Stripe => 'Stripe',
            self::PayPal => 'PayPal',
            self::BankTransfer => __('Virement bancaire'),
            self::Cash => __('Espèces'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Stripe => 'credit-card',
            self::PayPal => 'currency-dollar',
            self::BankTransfer => 'building-library',
            self::Cash => 'banknotes',
        };
    }

    public function isOnline(): bool
    {
        return in_array($this, [self::Stripe, self::PayPal]);
    }

    public function supportsRefund(): bool
    {
        return in_array($this, [self::Stripe, self::PayPal]);
    }

    public function fees(): float
    {
        return match ($this) {
            self::Stripe => 0.029, // 2.9%
            self::PayPal => 0.034, // 3.4%
            self::BankTransfer, self::Cash => 0.0,
        };
    }
}
