<?php

namespace crm\src\Investments\Balance\_common\DTOs;

/**
 * Входной DTO для обновления баланса.
 */
class InputInvBalanceDto
{
    public function __construct(
        public string $leadUid,
        public float $deposit = 0.0,
        public float $potation = 0.0,
    ) {
    }
}
