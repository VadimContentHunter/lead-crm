<?php

namespace crm\src\components\LeadManagement\_common\DTOs;

class LeadInputDto
{
    public function __construct(
        public ?int $id = null,
        public string $fullName = '',
        public string $address = '',
        public string $contact = '',
        public ?int $sourceId = null,
        public ?int $statusId = null,
        public ?int $accountManagerId = null,
        public ?string $groupName = null
    ) {
    }
}
