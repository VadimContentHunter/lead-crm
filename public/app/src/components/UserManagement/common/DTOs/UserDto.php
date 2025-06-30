<?php

namespace crm\src\components\UserManagement\common\DTOs;

class UserDto
{
    public function __construct(
        public string $login,
        public string $plainPassword,
        public string $passwordHash,
        public int $id = 0,
    ) {
    }
}
