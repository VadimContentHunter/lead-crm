<?php

namespace crm\src\Investments\InvStatus\_common\mappers;

use crm\src\Investments\_application\interfaces\AArrayNormalizer;

/**
 * Нормализатор массива для статуса.
 */
class InvStatusArrayNormalizer extends AArrayNormalizer
{
    /**
     * @param  array<string, mixed> $data
     * @return array{id: int|null, code: string|null, label: string|null}
     */
    public static function normalize(array $data): array
    {
        return [
            'id' => self::normalizeField($data, ['id', 'data-row-id', 'rowId'], fn($v) => (int) $v),
            'code' => self::normalizeFromNameValuePair($data, 'code', fn($v) => (string) $v)
                ?? self::normalizeField($data, ['code'], fn($v) => (string) $v),
            'label' => self::normalizeFromNameValuePair($data, 'label', fn($v) => (string) $v)
                ?? self::normalizeField($data, ['label'], fn($v) => (string) $v),
        ];
    }

    /**
     * @param array<string,mixed> $data
     */
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
