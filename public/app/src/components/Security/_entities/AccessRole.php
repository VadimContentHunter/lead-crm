<?php

namespace crm\src\components\Security\_entities;

class AccessRole
{
    public function __construct(
        public string $name,
        public ?int $id = null,
        public ?string $description = null,
    ) {
        if (trim($this->name) === '') {
            throw new \InvalidArgumentException('Role name cannot be empty');
        }
    }
}
