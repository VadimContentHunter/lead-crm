<?php

namespace crm\src\Investments\Comment\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\Comment\_entities\InvComment;
use crm\src\Investments\Comment\_common\InvCommentCollection;
use crm\src\Investments\Comment\_common\interfaces\ICommentResult;

class CommentResult extends AResult implements ICommentResult
{
    public function getComment(): ?InvComment
    {
        return $this->data instanceof InvComment ? $this->data : null;
    }

    public function getCollection(): ?InvCommentCollection
    {
        return $this->data instanceof InvCommentCollection
            ? $this->data
            : new InvCommentCollection([]);
    }

    public function getId(): ?int
    {
        return $this->getComment()?->id;
    }

    public function getLeadUid(): ?string
    {
        return $this->getComment()?->leadUid;
    }

    public function getBody(): ?string
    {
        return $this->getComment()?->body;
    }

    public function getWho(): ?string
    {
        return $this->getComment()?->who;
    }

    public function getWhoId(): ?string
    {
        return $this->getComment()?->whoId;
    }

    public function getOption(): ?int
    {
        return $this->getComment()?->option;
    }
}
