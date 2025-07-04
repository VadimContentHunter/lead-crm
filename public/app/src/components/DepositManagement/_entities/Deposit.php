<?php

namespace crm\src\components\DepositManagement\_entities;

class Deposit
{
    public function __construct(
        public int $leadId,
        public float $sum = 0.00,
        public ?int $id = null,
        public string $txId = '',
        public ?\DateTime $createdAt = null,
    ) {
    }
}
