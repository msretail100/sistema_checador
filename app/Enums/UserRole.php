<?php

namespace App\Enums;

enum UserRole: string
{
    case Root = 'root';
    case Admin = 'admin';
    case Worker = 'worker';

    public static function values(): array
    {
        return array_map(static fn (self $role): string => $role->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::Root => 'Root',
            self::Admin => 'Administrador',
            self::Worker => 'Trabajador',
        };
    }
}
