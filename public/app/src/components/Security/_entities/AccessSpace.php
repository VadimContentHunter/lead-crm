<?php

namespace crm\src\components\Security\_entities;

class AccessSpace
{
    public function __construct(
        public string $name,
        public ?int $id = null,
        public ?string $description = null,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Space name cannot be empty');
        }
    }
}
