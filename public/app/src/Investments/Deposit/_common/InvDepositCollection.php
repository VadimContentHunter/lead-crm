<?php

namespace crm\src\Investments\Deposit\_common;

use crm\src\Investments\Deposit\_entities\InvDeposit;

final class InvDepositCollection
{
    /**
     * @var Deposit[]
     */
    private array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(InvDeposit $deposit): void
    {
        $this->items[] = $deposit;
    }

    public function total(): float
    {
        return array_reduce($this->items, fn($carry, InvDeposit $d) => $carry + $d->sum, 0.0);
    }

    /**
     * @return Deposit[]
     */
    public function all(): array
    {
        return $this->items;
    }
}
