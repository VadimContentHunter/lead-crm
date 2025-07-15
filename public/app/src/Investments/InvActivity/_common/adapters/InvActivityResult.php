<?php

namespace crm\src\Investments\InvActivity\_common\adapters;

use Throwable;
use crm\src\Investments\InvActivity\_entities\InvInvActivity;
use crm\src\Investments\InvActivity\_common\InvInvActivityCollection;
use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;

class InvActivityResult extends AResult implements IInvActivityResult
{
    public function getInvActivity(): ?InvInvActivity
    {
        return $this->data instanceof InvInvActivity ? $this->data : null;
    }

    public function getHash(): ?string
    {
        return $this->getInvActivity()?->InvActivityHash;
    }

    public function getLeadUid(): ?string
    {
        return $this->getInvActivity()?->leadUid;
    }

    public function getAmount(): ?float
    {
        return $this->getInvActivity()?->amount;
    }

    public function getResult(): ?float
    {
        return $this->getInvActivity()?->result;
    }

    public function getId(): ?int
    {
        return $this->getInvActivity()?->id;
    }

    public function getCollection(): InvInvActivityCollection
    {
        return $this->data instanceof InvInvActivityCollection
            ? $this->data
            : new InvInvActivityCollection([]);
    }
}
