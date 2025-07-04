<?php

namespace crm\src\components\BalanceManagement\_common\mappers;

use crm\src\components\BalanceManagement\_entities\Balance;

class BalanceMapper
{
    /**
     * Преобразует массив данных из БД в объект Balance.
     *
     * @param  array<string, mixed> $data
     * @return Balance|null
     */
    public static function fromArray(array $data): ?Balance
    {
        if (!isset($data['lead_id'])) {
            return null;
        }

        return new Balance(
            leadId: (int)$data['lead_id'],
            current: isset($data['current']) ? (float)$data['current'] : 0.00,
            drain: isset($data['drain']) ? (float)$data['drain'] : 0.00,
            potential: isset($data['potential']) ? (float)$data['potential'] : 0.00,
            id: isset($data['id']) ? (int)$data['id'] : null
        );
    }

    /**
     * Преобразует объект Balance в массив для БД.
     *
     * @param  Balance $balance
     * @return array<string, mixed>
     */
    public static function toArray(Balance $balance): array
    {
        return [
            'id' => $balance->id,
            'lead_id' => $balance->leadId,
            'current' => $balance->current,
            'drain' => $balance->drain,
            'potential' => $balance->potential,
        ];
    }
}
