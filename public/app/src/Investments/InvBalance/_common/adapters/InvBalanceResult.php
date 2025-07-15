<?php

namespace crm\src\Investments\InvBalance\_common\adapters;

use Throwable;
use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvBalance\_entities\InvBalance;
use crm\src\Investments\InvBalance\_common\interfaces\IInvBalanceResult;

class InvBalanceResult extends AResult implements IInvBalanceResult
{
    public function getInvBalance(): ?InvBalance
    {
        return $this->data instanceof InvBalance ? $this->data : null;
    }

    public function getLeadUid(): ?string
    {
        return $this->getInvBalance()?->leadUid;
    }

    public function getCurrent(): ?float
    {
        return $this->getInvBalance()?->current;
    }

    public function getDeposit(): ?float
    {
        return $this->getInvBalance()?->deposit;
    }

    public function getPotation(): ?float
    {
        return $this->getInvBalance()?->potation;
    }

    public function getActive(): ?float
    {
        return $this->getInvBalance()?->active;
    }
}
