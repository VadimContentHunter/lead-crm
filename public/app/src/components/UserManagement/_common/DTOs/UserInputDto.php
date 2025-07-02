<?php

namespace crm\src\components\UserManagement\_common\DTOs;

class UserInputDto
{
    public function __construct(
        public string $login,
        public string $plainPassword = '',
        public string $confirmPassword = '',
        public int $id = 0,
    ) {
    }
}
