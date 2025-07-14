<?php

namespace crm\src\Investments\Activity\_common\adapters;

use Throwable;
use crm\src\Investments\Activity\_entities\InvActivity;
use crm\src\Investments\Activity\_common\InvActivityCollection;
use crm\src\_common\interfaces\AResult;
use crm\src\Investments\Activity\_common\interfaces\IActivityResult;

class ActivityResult extends AResult implements IActivityResult
{
    public function getActivity(): ?InvActivity
    {
        return $this->data instanceof InvActivity ? $this->data : null;
    }

    public function getHash(): ?string
    {
        return $this->getActivity()?->activityHash;
    }

    public function getLeadUid(): ?string
    {
        return $this->getActivity()?->leadUid;
    }

    public function getAmount(): ?float
    {
        return $this->getActivity()?->amount;
    }

    public function getResult(): ?float
    {
        return $this->getActivity()?->result;
    }

    public function getId(): ?int
    {
        return $this->getActivity()?->id;
    }

    public function getCollection(): InvActivityCollection
    {
        return $this->data instanceof InvActivityCollection
            ? $this->data
            : new InvActivityCollection([]);
    }
}
