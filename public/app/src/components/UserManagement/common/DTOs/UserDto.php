<?php

namespace crm\src\components\UserManagement\common\DTOs;

class UserDto
{
    public function __construct(
        public string $login,
        public string $plainPassword,
    ) {
    }
}
