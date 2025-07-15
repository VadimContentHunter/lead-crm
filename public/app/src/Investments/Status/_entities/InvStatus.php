<?php

namespace Domain\Investment;

class InvStatus
{
    public function __construct(
        public int $id,
        public string $code,   // например: work, lost, deal
        public string $label,  // Человеческое название: "В работе"
    ) {
    }
}
