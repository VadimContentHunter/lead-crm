<?php

namespace crm\src\components\Security\_entities;

class AccessContext
{
    public function __construct(
        public int $userId,
        public ?string $sessionAccessHash = null,
        public ?int $roleId = null,
        public ?int $spaceId = null,
        public ?int $id = null,
    ) {
        if ($this->userId === 0) {
            throw new \InvalidArgumentException('Access context userId cannot be empty');
        }
    }

    public function assignRole(int $roleId): void
    {
        $this->roleId = $roleId;
    }

    public function removeRole(): void
    {
        $this->roleId = null;
    }

    public function assignSpace(int $spaceId): void
    {
        $this->spaceId = $spaceId;
    }

    public function removeSpace(): void
    {
        $this->spaceId = null;
    }
}
