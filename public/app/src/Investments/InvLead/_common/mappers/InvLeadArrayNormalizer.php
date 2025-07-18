<?php

namespace crm\src\Investments\InvLead\_common\mappers;

use crm\src\Investments\_application\interfaces\AArrayNormalizer;

/**
 * Нормализатор данных для массивов инвестиционного лида.
 */
class InvLeadArrayNormalizer extends AArrayNormalizer
{
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

    public static function normalizeUid(array $data, array $keys = ['uid', 'lead_uid']): ?string
    {
        return self::normalizeField($data, $keys, fn($v) => (string) $v);
    }
}
