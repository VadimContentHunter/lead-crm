<?php

namespace crm\src\components\Security;

enum RoleNames: string
{
    case MANAGER = 'manager';
    case TEAM_MANAGER = 'team-manager';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'superadmin';

    /**
     * Универсальная проверка: соответствует ли строка любой роли в enum (игнорируя регистр).
     */
    public static function fromName(string $roleName): ?self
    {
        $normalizedRole = strtolower($roleName);

        foreach (self::cases() as $case) {
            if ($normalizedRole === $case->value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Проверяет, является ли роль менеджером.
     */
    public static function isManager(string $roleName): bool
    {
        return strtolower($roleName) === self::MANAGER->value;
    }

    /**
     * Проверяет, является ли роль тим-лидом.
     */
    public static function isTeamManager(string $roleName): bool
    {
        return strtolower($roleName) === self::TEAM_MANAGER->value;
    }

    /**
     * Проверяет, является ли роль обычным администратором.
     */
    public static function isAdmin(string $roleName): bool
    {
        return strtolower($roleName) === self::ADMIN->value;
    }

    /**
     * Проверяет, является ли роль супер-администратором.
     */
    public static function isSuperAdmin(string $roleName): bool
    {
        return strtolower($roleName) === self::SUPER_ADMIN->value;
    }

    /**
     * Групповая проверка: является ли роль каким-либо менеджером.
     */
    public static function isAnyManager(string $roleName): bool
    {
        return self::isManager($roleName) || self::isTeamManager($roleName);
    }

    /**
     * Групповая проверка: является ли роль любым администратором.
     */
    public static function isAnyAdmin(string $roleName): bool
    {
        return self::isAdmin($roleName) || self::isSuperAdmin($roleName);
    }
}
