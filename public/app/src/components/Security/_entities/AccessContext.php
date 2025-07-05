<?php

namespace crm\src\components\Security\_entities;

class AccessContext
{
    public function __construct(
        public ?int $userId = null,
        public ?string $sessionAccessHash = null,
        public ?int $id = null,
    ) {
    }
    // public int $roleId;
    // public int $spaceId;
}
