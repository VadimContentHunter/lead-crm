<?php

namespace crm\src\Investments\InvActivity\_entities;

enum DealType: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';

    /**
     * Проверяет, равны ли два значения типа сделки.
     *
     * @param  DealType|string $a
     * @param  DealType|string $b
     * @return bool
     */
    public static function equals(DealType|string $a, DealType|string $b): bool
    {
        $aVal = $a instanceof self ? $a->value : (string) $a;
        $bVal = $b instanceof self ? $b->value : (string) $b;

        return $aVal === $bVal;
    }
}
