<?php

namespace crm\src\Investments\Deposit\_entities;

use DateTimeImmutable;

class InvDeposit
{
    public function __construct(
        public DateTimeImmutable $occurredAt,
        public float $sum,
        public string $comment = '',
    ) {
    }
}
