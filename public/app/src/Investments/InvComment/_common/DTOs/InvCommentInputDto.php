<?php

namespace crm\src\Investments\InvComment\_common\DTOs;

class InvCommentInputDto
{
    public function __construct(
        public ?string $leadUid = null,
        public ?string $body = null,
        public ?string $who = null,
        public ?string $whoId = null,
        public ?int $option = null,
        public ?int $id = null,
    ) {
    }
}
