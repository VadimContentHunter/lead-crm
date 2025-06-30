<?php

namespace crm\src\components\LeadManagement\_common\DTOs;

class SourceDto
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
    ) {
    }
}
