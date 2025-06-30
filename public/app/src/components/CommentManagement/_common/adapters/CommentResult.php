<?php

namespace crm\src\components\CommentManagement\_common\adapters;

use DateTime;
use Throwable;
use crm\src\_common\interfaces\AResult;
use crm\src\components\CommentManagement\_entities\Comment;
use crm\src\components\CommentManagement\_common\interfaces\ICommentResult;

class CommentResult extends AResult implements ICommentResult
{
    public function getComment(): ?Comment
    {
        return $this->data instanceof Comment ? $this->data : null;
    }

    public function getId(): ?int
    {
        return $this->getComment()?->id;
    }

    public function getCommentText(): ?string
    {
        return $this->getComment()?->comment;
    }

    public function getUserId(): ?int
    {
        return $this->getComment()?->userId;
    }

    public function getLeadId(): ?int
    {
        return $this->getComment()?->leadId;
    }

    public function getDepositId(): ?int
    {
        return $this->getComment()?->depositId;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->getComment()?->createdAt;
    }
}
