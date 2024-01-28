<?php

namespace App\Enums;

enum PlayerPosition: string
{
    case DEFENDER = 'defender';
    case MIDFIELDER = 'midfielder';
    case FORWARD = 'forward';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
