<?php

namespace App\Enums;

enum Role: int
{
    case ADMIN = 1;
    case OWNER = 2;
    case COMMON_USER = 3;

    public function name(): string
    {
        return match ($this) {
            self::ADMIN => __('Admin'),
            self::OWNER => __('Proprietário'),
            self::COMMON_USER => __('Usuário Comum'),
        };
    }
}