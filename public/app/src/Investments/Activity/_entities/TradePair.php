<?php

namespace crm\src\Investments\Activity\_entities;

enum TradePair: string
{
    case BTC_USDT = 'BTC/USDT';
    case ETH_USDT = 'ETH/USDT';
    case SOL_USDT = 'SOL/USDT';
    case XRP_USDT = 'XRP/USDT';

    /**
     * Проверка: входит ли в список enum-значений.
     */
    public static function isKnown(string $value): bool
    {
        return !is_null(self::tryFrom($value));
    }

    /**
     * Получить значение: либо из enum, либо оставить как есть.
     */
    public static function normalize(string $value): string
    {
        return self::isKnown($value) ? self::from($value)->value : $value;
    }
}
