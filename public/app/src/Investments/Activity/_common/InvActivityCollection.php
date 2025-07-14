<?php

namespace crm\src\Investments\Activity\_common;

use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_entities\DealType;

final class InvActivityCollection
{
    /**
     * @param InvActivity[] $items
     */
    public function __construct(
        private array $items = []
    ) {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(InvActivity $InvActivity): void
    {
        $this->items[] = $InvActivity;
    }

    /**
     * @return InvActivity[]
     */
    public function active(): array
    {
        return array_filter($this->items, fn(InvActivity $a) => $a->type === DealType::ACTIVE);
    }

    /**
     * @return InvActivity[]
     */
    public function closed(): array
    {
        return array_filter($this->items, fn(InvActivity $a) => $a->type === DealType::CLOSED);
    }

    /**
     * @return InvActivity[]
     */
    public function all(): array
    {
        return $this->items;
    }
}
