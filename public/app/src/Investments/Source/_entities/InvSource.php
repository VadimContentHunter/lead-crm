<?php

namespace crm\src\Investments\Source\_entities;

class InvSource
{
    public function __construct(
        public string $code,       // bybit, binance, telegram и т.п.
        public string $name,       // Человеческое название: "Bybit"
    ) {
    }
}
