<?php

namespace crm\src\Investments\Deposit\_common;

use crm\src\Investments\Deposit\_entities\InvDeposit;

final class InvDepositCollection
{
    /**
     * @param InvDeposit[] $items
     */
    public function __construct(
        private array $items = []
    ) {
        foreach ($this->items as $item) {
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
     * @return InvDeposit[]
     */
    public function all(): array
    {
        return $this->items;
    }
}
