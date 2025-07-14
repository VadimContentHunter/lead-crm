<?php

namespace crm\src\Investments\Activity\_common;

use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_entities\DealType;

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
     * @param  InvActivity $activity Сделка для добавления
     * @return void
     */
    public function add(InvActivity $activity): void
    {
        $this->items[] = $activity;
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
