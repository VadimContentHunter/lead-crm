<?php

namespace crm\src\Investments\Balance\_entities;

class InvBalance
{
    public function __construct(
        public float $current = 0.0,
        public float $deposit = 0.0,
        public float $potation = 0.0,
        public float $active = 0.0,
    ) {
    }
}
