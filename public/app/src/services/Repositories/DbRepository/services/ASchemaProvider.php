<?php

namespace crm\src\services\Repositories\DbRepository\services;

/**
 * Поставщик SQL-схем таблиц.
 */
abstract class ASchemaProvider
{
    abstract protected static function schemas(): array;

    public static function get(string $table): ?string
    {
        return static::schemas()[$table] ?? null;
    }

    public static function has(string $table): bool
    {
        return array_key_exists($table, static::schemas());
    }
}
