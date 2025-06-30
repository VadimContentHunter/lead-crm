<?php

namespace crm\src\components\StatusManagement\_entities;

class Status
{
    public function __construct(
        public string $title,
        public ?int $id = null,
    ) {
    }
}
