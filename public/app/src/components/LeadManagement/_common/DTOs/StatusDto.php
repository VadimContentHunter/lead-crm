<?php

namespace crm\src\components\LeadManagement\_common\DTOs;

class StatusDto
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
    ) {
    }
}
