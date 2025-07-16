<?php

namespace crm\src\Investments\InvSource\_entities;

class InvSource
{
    public function __construct(
        public string $code,       // bybit, binance, telegram и т.п.
        public string $label,       // Человеческое название: "Bybit"
    ) {
    }
}
