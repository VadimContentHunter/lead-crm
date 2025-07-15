<?php

namespace crm\src\Investments\Comment\_common\DTOs;

class DbInvCommentDto
{
    public function __construct(
        public string $id,
        public string $lead_uid,
        public string $body,
        public string $time,      // ISO строка для совместимости с SQL
        public string $who,
        public ?string $who_id,
        public int $option = 0,
    ) {
    }
}
