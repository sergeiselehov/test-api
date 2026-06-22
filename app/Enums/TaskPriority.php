<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskPriority: string
{
    case Low = 'низкий';
    case Medium = 'средний';
    case High = 'высокий';

    public static function values(): array
    {
        return array_map(static fn (self $case) => $case->value, self::cases());
    }
}
