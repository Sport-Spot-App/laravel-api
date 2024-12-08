<?php

namespace App\Enums;

enum Role: int
{
    case admin = 1;
    case owner = 2;
    case common_user = 3;

    public function name(): string
    {
        return match ($this) {
            self::admin => __('Admin'),
            self::owner => __('Proprietário'),
            self::common_user => __('Usuário Comum'),
        };
    }
}