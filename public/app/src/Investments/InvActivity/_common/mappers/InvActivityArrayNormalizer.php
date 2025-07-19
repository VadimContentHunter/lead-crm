<?php

namespace crm\src\Investments\InvActivity\_common\mappers;

use crm\src\Investments\_application\interfaces\AArrayNormalizer;

class InvActivityArrayNormalizer extends AArrayNormalizer
{
    /**
     * @param  array<string, mixed> $raw
     * @return array<string, mixed>
     */
    public static function normalize(array $raw): array
    {
        return [
            'id' => self::normalizeField($raw, ['id', 'data-row-id', 'rowId'], fn($v) => (int) $v),
            'activity_hash' => self::normalizeField($raw, ['activity_hash']),
            'lead_uid' => self::normalizeField($raw, ['lead_uid', 'uid'], fn($v) => (string) $v),
            'type' => self::normalizeField($raw, ['type'], fn($v) => (string) $v),
            'open_time' => self::normalizeField($raw, ['open_time'], fn($v) => (string) $v),
            'close_time' => self::normalizeField($raw, ['close_time'], fn($v) => (string) $v),
            'pair' => self::normalizeField($raw, ['pair'], fn($v) => (string) $v),
            'open_price' => self::normalizeField($raw, ['open_price'], fn($v) => (float) $v),
            'close_price' => self::normalizeField($raw, ['close_price'], fn($v) => (float) $v),
            'amount' => self::normalizeField($raw, ['amount'], fn($v) => (float) $v),
            'direction' => self::normalizeField($raw, ['direction'], fn($v) => (string) $v),
            'result' => self::normalizeField($raw, ['result'], fn($v) => (float) $v),
        ];
    }

    /**
     * @param  array<string,mixed> $data
     * @return mixed
     */
    public static function normalizeByFieldName(string $field, array $data): mixed
    {
        return match ($field) {
            'id' => self::normalizeField($data, ['id', 'data-row-id', 'rowId'], fn($v) => (int) $v),
            'activity_hash' => self::normalizeField($data, ['activity_hash'], fn($v) => (string) $v),
            'lead_uid' => self::normalizeField($data, ['lead_uid', 'uid'], fn($v) => (string) $v),
            'type' => self::normalizeField($data, ['type'], fn($v) => (string) $v),
            'open_time' => self::normalizeField($data, ['open_time'], fn($v) => (string) $v),
            'close_time' => self::normalizeField($data, ['close_time'], fn($v) => (string) $v),
            'pair' => self::normalizeField($data, ['pair'], fn($v) => (string) $v),
            'open_price' => self::normalizeField($data, ['open_price'], fn($v) => (float) $v),
            'close_price' => self::normalizeField($data, ['close_price'], fn($v) => (float) $v),
            'amount' => self::normalizeField($data, ['amount'], fn($v) => (float) $v),
            'direction' => self::normalizeField($data, ['direction'], fn($v) => (string) $v),
            'result' => self::normalizeField($data, ['result'], fn($v) => (float) $v),
            default => $data[$field] ?? null,
        };
    }
}
