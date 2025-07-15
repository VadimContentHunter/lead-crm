<?php

namespace crm\src\Investments\InvActivity\_common;

use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\_entities\DealType;

/**
 * Коллекция инвестиционных сделок (InvActivity).
 * Позволяет управлять списком сделок и фильтровать их по состоянию.
 */
final class InvActivityCollection
{
    /**
     * @var InvActivity[] $items Массив сделок
     */
    private array $items = [];

    /**
     * @param InvActivity[] $items Изначальный список сделок
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Добавляет сделку в коллекцию.
     *
     * @param  InvActivity $InvActivity Сделка для добавления
     * @return void
     */
    public function add(InvActivity $InvActivity): void
    {
        $this->items[] = $InvActivity;
    }

    /**
     * Возвращает список всех активных сделок.
     *
     * @return InvActivity[]
     */
    public function getActive(): array
    {
        return array_filter($this->items, fn(InvActivity $a) => $a->type === DealType::ACTIVE);
    }

    /**
     * Возвращает список всех закрытых сделок.
     *
     * @return InvActivity[]
     */
    public function getClosed(): array
    {
        return array_filter($this->items, fn(InvActivity $a) => $a->type === DealType::CLOSED);
    }

    /**
     * Возвращает все сделки (и активные, и закрытые).
     *
     * @return InvActivity[]
     */
    public function getAll(): array
    {
        return $this->items;
    }
}
