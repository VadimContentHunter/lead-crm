<?php

namespace crm\src\Investments\InvActivity\_common\adapters;

use Throwable;
use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\_common\InvActivityCollection;
use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvActivity\_common\interfaces\IInvActivityResult;

class InvActivityResult extends AResult implements IInvActivityResult
{
    public function getInvActivity(): ?InvActivity
    {
        return $this->data instanceof InvActivity ? $this->data : null;
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

    public function getCollection(): InvActivityCollection
    {
        return $this->data instanceof InvActivityCollection
            ? $this->data
            : new InvActivityCollection([]);
    }

    /**
     * @return string|null
     */
    public function getPair(): ?string
    {
        return $this->getInvActivity()?->pair;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->getInvActivity()?->type->value;
    }
}
