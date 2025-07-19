<?php

namespace crm\src\Investments\InvBalance\_common\mappers;

use crm\src\Investments\_application\interfaces\AArrayNormalizer;

/**
 * Нормализатор массива для баланса.
 */
class InvBalanceArrayNormalizer extends AArrayNormalizer
{
    /**
     * @param array<string,mixed> $data
     */
    public static function normalizeByFieldName(string $field, array $data): mixed
    {
        return match ($field) {
            'lead_uid' => self::normalizeField($data, ['lead_uid', 'uid'], fn($v) => (string) $v),
            'deposit' => self::normalizeField($data, ['deposit'], fn($v) => (float) $v),
            'potential' => self::normalizeField($data, ['potential'], fn($v) => (float) $v),
            'current' => self::normalizeField($data, ['current'], fn($v) => (float) $v),
            'active' => self::normalizeField($data, ['active'], fn($v) => (float) $v),
            default => $data[$field] ?? null,
        };
    }
}
