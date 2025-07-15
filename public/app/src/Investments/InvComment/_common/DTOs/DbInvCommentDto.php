<?php

namespace crm\src\Investments\InvComment\_common\DTOs;

class DbInvCommentDto
{
    public function __construct(
        public string $lead_uid,
        public string $body,
        public string $time,
        public ?string $who = '',
        public ?string $who_id = null,
        public ?int $option = 0,
        public ?int $id = null,
    ) {
    }
}
