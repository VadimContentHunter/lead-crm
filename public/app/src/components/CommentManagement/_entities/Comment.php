<?php

namespace crm\src\components\CommentManagement\_entities;

use DateTime;

class Comment
{
    public function __construct(
        public string $comment,
        public int $leadId,
        public ?int $userId = null,
        public ?int $id = null,
        public ?int $depositId = null,
        public ?DateTime $createdAt = null,
    ) {
    }
}
