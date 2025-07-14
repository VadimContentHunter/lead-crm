<?php

namespace crm\src\Investments\Comment\_entities;

class InvComment
{
    public function __construct(
        public string $body,
        public int $time,
        public string $who,
    ) {
    }
}
