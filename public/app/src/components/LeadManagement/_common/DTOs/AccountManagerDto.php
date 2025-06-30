<?php

namespace crm\src\components\LeadManagement\_common\DTOs;

class AccountManagerDto
{
    public function __construct(
        public ?int $id = null,
        public string $login = '',
    ) {
    }
}
