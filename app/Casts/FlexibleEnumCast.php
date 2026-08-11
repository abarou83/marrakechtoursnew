<?php

declare(strict_types=1);

namespace App\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class FlexibleEnumCast implements CastsAttributes
{
    public function __construct(
        protected string $enumClass
    ) {}

    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return $value;
        }

        if (method_exists($this->enumClass, 'tryFromValue')) {
            return $this->enumClass::tryFromValue((string) $value);
        }

        return $this->enumClass::tryFrom((string) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        return $value;
    }
}
