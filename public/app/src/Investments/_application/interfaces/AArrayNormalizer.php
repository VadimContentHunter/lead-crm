<?php

namespace crm\src\Investments\_application\interfaces;

/**
 * Абстрактный нормализатор массивов.
 * Содержит общие утилиты для приведения данных из form-data, inline-редакторов и т.д.
 */
abstract class AArrayNormalizer
{
    /**
     * Нормализует значение по списку возможных ключей.
     *
     * @param array<string, mixed> $data
     * @param array<int, string> $keys
     * @param callable|null $cast
     */
    public static function normalizeField(array $data, array $keys, ?callable $cast = null): mixed
    {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                return $cast ? $cast($data[$key]) : $data[$key];
            }
        }

        return null;
    }

    /**
     * Проверяет, соответствует ли поле name указанному ключу, и возвращает value, если да.
     *
     * @param array<string,mixed> $data
     * @param callable|null $cast
     */
    public static function normalizeFromNameValuePair(array $data, string $expectedField, ?callable $cast = null): mixed
    {
        if (isset($data['name'], $data['value']) && $data['name'] === $expectedField) {
            return $cast ? $cast($data['value']) : $data['value'];
        }

        return null;
    }
}
