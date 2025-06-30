<?php

namespace crm\src\components\UserManagement\common\DTOs;

class UserInputDto
{
    public function __construct(
        public string $login,
        public string $plainPassword = '',
        public int $id = 0,
    ) {
    }
}
