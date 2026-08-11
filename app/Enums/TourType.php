<?php

declare(strict_types=1);

namespace App\Enums;

enum TourType: string
{
    case Daytrip = 'daytrip';
    case Activity = 'activity';
    case Excursion = 'excursion';
    case Circuit = 'circuit';
    case DayTrip = 'day_trip';
    case MultiDay = 'multi_day';
    case Group = 'group';
    case Private = 'private';
    case Shared = 'shared';

    public static function tryFromValue(?string $value): ?self
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::tryFrom($value) ?? match ($value) {
            'daytrip' => self::Daytrip,
            'circuit' => self::Circuit,
            default => self::Activity,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Daytrip, self::DayTrip => __('Journée'),
            self::Activity => __('Activité'),
            self::Excursion => __('Excursion'),
            self::Circuit, self::MultiDay => __('Circuit'),
            self::Group => __('Groupe'),
            self::Private => __('Privé'),
            self::Shared => __('Partagé'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Daytrip, self::DayTrip => __('Sortie d\'une journée complète'),
            self::Activity => __('Activité courte ou expérience locale'),
            self::Excursion => __('Excursion guidée d\'une demi-journée ou journée'),
            self::Circuit, self::MultiDay => __('Voyage sur plusieurs jours'),
            self::Group => __('Rejoignez un groupe de voyageurs pour une expérience conviviale'),
            self::Private => __('Tour exclusif pour vous et vos proches'),
            self::Shared => __('Partagez le véhicule avec d\'autres voyageurs'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Activity => 'sparkles',
            self::Excursion => 'map',
            self::Daytrip, self::DayTrip => 'sun',
            self::Circuit, self::MultiDay => 'calendar-days',
            self::Group => 'users',
            self::Private => 'user',
            self::Shared => 'user-group',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Activity => 'info',
            self::Excursion => 'success',
            self::Daytrip, self::DayTrip => 'warning',
            self::Circuit, self::MultiDay => 'danger',
            self::Group => 'secondary',
            self::Private => 'primary',
            self::Shared => 'accent',
        };
    }
}
