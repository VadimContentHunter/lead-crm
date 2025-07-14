<?php

namespace crm\src\Investments\Deposit\_common;

use crm\src\Investments\Deposit\_entities\InvDeposit;

/**
 * Коллекция депозитов инвестиционного лида.
 */
final class InvDepositCollection
{
    /**
     * @var InvDeposit[] Список депозитов
     */
    private array $items = [];

    /**
     * @param InvDeposit[] $items Изначальный список депозитов
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Добавляет депозит в коллекцию.
     *
     * @param  InvDeposit $deposit Депозит для добавления
     * @return void
     */
    public function add(InvDeposit $deposit): void
    {
        $this->items[] = $deposit;
    }

    /**
     * Возвращает сумму всех депозитов в коллекции.
     *
     * @return float Общая сумма
     */
    public function getTotal(): float
    {
        return array_reduce($this->items, fn($carry, InvDeposit $d) => $carry + $d->sum, 0.0);
    }

    /**
     * Возвращает все депозиты.
     *
     * @return InvDeposit[]
     */
    public function getAll(): array
    {
        return $this->items;
    }
}
