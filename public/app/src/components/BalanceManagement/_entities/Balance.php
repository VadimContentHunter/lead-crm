<?php

namespace crm\src\components\BalanceManagement\_entities;

class Balance
{
    public function __construct(
        public int $leadId,
        public float $current = 0.00,
        public float $drain = 0.00,
        public float $potential = 0.00,
        public ?int $id = null,
    ) {
    }
}
