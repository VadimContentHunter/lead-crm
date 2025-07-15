<?php

namespace crm\src\Investments\InvBalance\_common\DTOs;

/**
 * DTO для работы с БД: баланс инвестиционного лида.
 */
class DbInvBalanceDto
{
    public function __construct(
        public string $lead_uid,
        public float $current,
        public float $deposit,
        public float $potation,
        public float $active,
    ) {
    }
}
