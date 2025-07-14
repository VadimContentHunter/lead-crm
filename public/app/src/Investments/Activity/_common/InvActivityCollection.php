<?php

namespace crm\src\Investments\Activity\_common;

use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_entities\DealType;

final class InvActivityCollection
{
    /**
     * @var InvActivity[]
     */
    private array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    public function add(InvActivity $InvActivity): void
    {
        $this->items[] = $InvActivity;
    }

    public function active(): array
    {
        return array_filter($this->items, fn(InvActivity $a) => $a->type === DealType::ACTIVE);
    }

    public function closed(): array
    {
        return array_filter($this->items, fn(InvActivity $a) => $a->type === DealType::CLOSED);
    }

    public function all(): array
    {
        return $this->items;
    }
}
