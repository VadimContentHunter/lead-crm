<?php

namespace crm\src\components\LeadManagement\_entities;

use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use DateTime;

class Lead
{
    public function __construct(
        public ?int $id = null,
        public string $fullName,
        public string $contact,
        public string $address = '',
        public ?SourceDto $source = null,
        public ?StatusDto $status = null,
        public ?AccountManagerDto $accountManager = null,
        public ?DateTime $createdAt = null,
    ) {
    }
}
