<?php

namespace crm\src\Investments\Activity\_entities;

class InvActivity
{
    public function __construct(
        public string $id,
        public DealType $type,
        public int $openTime,
        public ?int $closeTime,
        public string $pair,
        public float $openPrice,
        public ?float $closePrice,
        public float $amount,
        public DealDirection $direction,
        public ?float $result,
    ) {
    }
}
