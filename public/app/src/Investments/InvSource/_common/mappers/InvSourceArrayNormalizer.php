<?php

namespace crm\src\Investments\InvSource\_common\mappers;

use crm\src\Investments\_application\interfaces\AArrayNormalizer;

class InvSourceArrayNormalizer extends AArrayNormalizer
{
    public static function normalize(array $raw): array
    {
        return [
            'id' => self::normalizeField($raw, ['id', 'data-row-id'], fn($v) => (int) $v),
            'code' => $raw['name'] === 'code' ? (string) $raw['value'] : self::normalizeField($raw, ['code'], fn($v) => (string) $v),
            'label' => $raw['name'] === 'label' ? (string) $raw['value'] : self::normalizeField($raw, ['label'], fn($v) => (string) $v),
        ];
    }

    /**
     * Проверяет, соответствует ли поле name указанному ключу, и возвращает value, если да.
     *
     * @param  array<string, mixed> $data
     * @param  string $expectedField
     * @param  callable|null $cast
     * @return mixed|null
     */
    public static function normalizeFromNameValuePair(array $data, string $expectedField, ?callable $cast = null): mixed
    {
        if (isset($data['name'], $data['value']) && $data['name'] === $expectedField) {
            return $cast ? $cast($data['value']) : $data['value'];
        }

        return null;
    }


    public static function normalizeByFieldName(string $field, array $data): mixed
    {
        return match ($field) {
            'id' => self::normalizeField($data, ['id', 'data-row-id', 'rowId'], fn($v) => (int) $v),

            'code' => self::normalizeFromNameValuePair($data, 'code', fn($v) => (string) $v)
            ?? self::normalizeField($data, ['code'], fn($v) => (string) $v),

            'label' => self::normalizeFromNameValuePair($data, 'label', fn($v) => (string) $v)
            ?? self::normalizeField($data, ['label'], fn($v) => (string) $v),

            default => $data[$field] ?? null,
        };
    }
}
