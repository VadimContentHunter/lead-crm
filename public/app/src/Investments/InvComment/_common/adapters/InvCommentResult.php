<?php

namespace crm\src\Investments\InvComment\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvComment\_entities\InvComment;
use crm\src\Investments\InvComment\_common\InvCommentCollection;
use crm\src\Investments\InvComment\_common\interfaces\IInvCommentResult;

class InvCommentResult extends AResult implements IInvCommentResult
{
    public function getInvComment(): ?InvComment
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
        return $this->getInvComment()?->id;
    }

    public function getLeadUid(): ?string
    {
        return $this->getInvComment()?->leadUid;
    }

    public function getBody(): ?string
    {
        return $this->getInvComment()?->body;
    }

    public function getWho(): ?string
    {
        return $this->getInvComment()?->who;
    }

    public function getWhoId(): ?string
    {
        return $this->getInvComment()?->whoId;
    }

    public function getOption(): ?int
    {
        return $this->getInvComment()?->option;
    }
}
