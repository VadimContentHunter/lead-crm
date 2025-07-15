<?php

namespace crm\src\Investments\InvActivity\_common;

use crm\src\Investments\InvActivity\_entities\InvInvActivity;
use crm\src\Investments\InvActivity\_entities\DealType;

/**
 * Коллекция инвестиционных сделок (InvInvActivity).
 * Позволяет управлять списком сделок и фильтровать их по состоянию.
 */
final class InvInvActivityCollection
{
    /**
     * @var InvInvActivity[] $items Массив сделок
     */
    private array $items = [];

    /**
     * @param InvInvActivity[] $items Изначальный список сделок
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
     * @param  InvInvActivity $InvActivity Сделка для добавления
     * @return void
     */
    public function add(InvInvActivity $InvActivity): void
    {
        $this->items[] = $InvActivity;
    }

    /**
     * Возвращает список всех активных сделок.
     *
     * @return InvInvActivity[]
     */
    public function getActive(): array
    {
        return array_filter($this->items, fn(InvInvActivity $a) => $a->type === DealType::ACTIVE);
    }

    /**
     * Возвращает список всех закрытых сделок.
     *
     * @return InvInvActivity[]
     */
    public function getClosed(): array
    {
        return array_filter($this->items, fn(InvInvActivity $a) => $a->type === DealType::CLOSED);
    }

    /**
     * Возвращает все сделки (и активные, и закрытые).
     *
     * @return InvInvActivity[]
     */
    public function getAll(): array
    {
        return $this->items;
    }
}
