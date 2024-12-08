<?php

namespace App\Enums;

enum Role: int
{
    case ADMIN = 1;
    case OWNER = 2;
    case ATHLETE = 3;

    public function name(): string
    {
        return match ($this) {
            self::ADMIN => __('Admin'),
            self::OWNER => __('Proprietário'),
            self::ATHLETE => __('Atleta'),
        };
    }
}