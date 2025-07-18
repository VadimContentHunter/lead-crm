<?php

namespace crm\src\Investments\InvLead\_common\mappers;

/**
 * Нормализатор данных для массивов инвестиционного лида.
 * Используется для преобразования входных массивов в стандартизированные значения.
 */
class InvLeadArrayNormalizer
{
    /**
     * Нормализует значение по списку возможных ключей.
     *
     * @param array<string,mixed> $data
     * @param array<int,string> $keys
     * @param callable|null $cast Функция преобразования (например: fn($v) => (int) $v)
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
     * Нормализует значение по имени поля, используя match.
     *
     * @param  string $field
     * @param  array<string,mixed> $data
     * @return mixed
     */
    public static function normalizeByFieldName(string $field, array $data): mixed
    {
        return match ($field) {
            'uid' => self::normalizeUid($data),
            'contact' => self::normalizeField($data, ['contact'], fn($v) => (string) $v),
            'phone' => self::normalizeField($data, ['phone'], fn($v) => (string) $v),
            'email' => self::normalizeField($data, ['email'], fn($v) => (string) $v),
            'full_name' => self::normalizeField($data, ['full_name'], fn($v) => (string) $v),
            'account_manager_id' => self::normalizeField($data, ['account_manager_id'], fn($v) => (int) $v),
            'visible' => self::normalizeField($data, ['visible'], fn($v) => (bool) $v),
            'source_id' => self::normalizeField($data, ['source_id'], fn($v) => (int) $v),
            'status_id' => self::normalizeField($data, ['status_id'], fn($v) => (int) $v),
            default => $data[$field] ?? null,
        };
    }


    /**
     * Частный случай для UID (строка).
     *
     * @param  array<string, mixed> $data
     * @param  array<int, string> $keys
     * @return string|null
     */
    public static function normalizeUid(array $data, array $keys = ['uid', 'lead_uid']): ?string
    {
        return self::normalizeField($data, $keys, fn($v) => (string) $v);
    }
}
