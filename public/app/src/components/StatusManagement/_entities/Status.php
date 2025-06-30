<?php

namespace crm\src\components\StatusesManagement\_entities;

class Status
{
    public function __construct(
        public string $title,
        public ?int $id = null,
    ) {
    }
}
