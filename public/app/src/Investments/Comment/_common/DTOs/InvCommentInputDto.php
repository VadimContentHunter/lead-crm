<?php

namespace crm\src\Investments\Comment\_common\DTOs;

class InvCommentInputDto
{
    public function __construct(
        public string $leadUid,
        public string $body,
        public ?string $who = '',
        public ?string $whoId = null,
        public int $option = 0,
    ) {
    }
}
