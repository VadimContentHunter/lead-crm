<?php

namespace crm\src\components\DepositManagement\_common\mappers;

use crm\src\components\DepositManagement\_entities\Deposit;

class DepositMapper
{
    /**
     * Преобразует массив данных из БД в объект Deposit.
     *
     * @param  array<string, mixed> $data
     * @return Deposit|null
     */
    public static function fromArray(array $data): ?Deposit
    {
        if (!isset($data['lead_id'])) {
            return null;
        }

        return new Deposit(
            leadId: (int)$data['lead_id'],
            sum: isset($data['sum']) ? (float)$data['sum'] : 0.00,
            id: isset($data['id']) ? (int)$data['id'] : null,
            txId: isset($data['tx_id']) ? (string)$data['tx_id'] : '',
            createdAt: isset($data['created_at']) ? new \DateTime($data['created_at']) : null
        );
    }

    /**
     * Преобразует объект Deposit в массив для БД.
     *
     * @param  Deposit $deposit
     * @return array<string, mixed>
     */
    public static function toArray(Deposit $deposit): array
    {
        return [
            'id' => $deposit->id,
            'lead_id' => $deposit->leadId,
            'sum' => $deposit->sum,
            'tx_id' => $deposit->txId,
            'created_at' => $deposit->createdAt?->format('Y-m-d H:i:s'),
        ];
    }
}
