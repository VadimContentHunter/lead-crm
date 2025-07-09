<?php

namespace crm\src\components\LeadManagement\_common\DTOs;

class LeadCommentDto
{
    public function __construct(
        public ?int $id = null,
        public string $fullName = '',
        public string $contact = '',
        public string $address = '',
        public ?int $sourceId = null,
        public string $sourceTitle = '',
        public ?int $statusId = null,
        public string $statusTitle = '',
        public ?int $accountManagerId = null,
        public string $accountManagerLogin = '',
        public ?string $groupName = null,
    ) {
    }
}
