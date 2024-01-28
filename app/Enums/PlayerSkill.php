<?php

namespace App\Enums;

enum PlayerSkill: string
{
    case DEFENSE = 'defense';
    case ATTACK = 'attack';
    case SPEED = 'speed';
    case STRENGTH = 'strength';
    case STAMINA = 'stamina';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
