<?php

namespace crm\src\components\LeadManagement\_common\DTOs;

class LeadFilterDto
{
    public function __construct(
        public ?string $search = null,
        public ?string $manager = null,
        public ?string $status = null,
        public ?string $source = null,
        public ?float $potentialMin = null,
        public ?float $balanceMin = null,
        public ?float $drainMin = null,
    ) {
    }
}
