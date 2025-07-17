<?php

namespace crm\src\Investments\InvBalance\_entities;

/**
 * Баланс инвестиционного лида.
 */
class InvBalance
{
    /**
     * @param string $leadUid   UID лида, к которому относится этот баланс
     * @param float $current   Текущий баланс по сайту "Инвестки"
     * @param float $deposit   Сумма, добавленная на баланс
     * @param float $potential Сколько ещё можно добавить (лимит пополнения)
     * @param float $active    Остаток на кошельке (информация со стороннего сайта)
     */
    public function __construct(
        public string $leadUid,
        public float $current = 0.0,
        public float $deposit = 0.0,
        public float $potential = 0.0,
        public float $active = 0.0,
    ) {
    }
}
