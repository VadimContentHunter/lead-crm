<?php

namespace crm\src\components\UserManagement\_entities;

class User
{
    public function __construct(
        public string $login,
        public string $passwordHash,
        public ?int $id = null,
    ) {
    }
}
