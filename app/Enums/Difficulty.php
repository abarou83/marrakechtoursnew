<?php

declare(strict_types=1);

namespace App\Enums;

enum Difficulty: string
{
    case Easy = 'easy';
    case Moderate = 'moderate';
    case Challenging = 'challenging';

    public function label(): string
    {
        return match ($this) {
            self::Easy => __('Facile'),
            self::Moderate => __('Modéré'),
            self::Challenging => __('Difficile'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Easy => __('Accessible à tous, peu de marche'),
            self::Moderate => __('Quelques efforts physiques requis'),
            self::Challenging => __('Bonne condition physique requise'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Easy => 'success',
            self::Moderate => 'warning',
            self::Challenging => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Easy => 'face-smile',
            self::Moderate => 'bolt',
            self::Challenging => 'fire',
        };
    }

    public function minimumAge(): int
    {
        return match ($this) {
            self::Easy => 0,
            self::Moderate => 6,
            self::Challenging => 12,
        };
    }
}
