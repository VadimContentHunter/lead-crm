<?php

namespace crm\src\components\UserManagement\_common\DTOs;

class UserFilterDto
{
    public function __construct(
        public ?string $search = null,
        public ?string $login = null,
        public ?string $sort = null,
        public ?string $sortDir = null,
    ) {
    }
}
