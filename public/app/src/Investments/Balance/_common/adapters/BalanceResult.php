<?php

namespace crm\src\Investments\Balance\_common\adapters;

use Throwable;
use crm\src\_common\interfaces\AResult;
use crm\src\Investments\Balance\_entities\InvBalance;
use crm\src\Investments\Balance\_common\interfaces\IBalanceResult;

class BalanceResult extends AResult implements IBalanceResult
{
    public function getBalance(): ?InvBalance
    {
        return $this->data instanceof InvBalance ? $this->data : null;
    }

    public function getLeadUid(): ?string
    {
        return $this->getBalance()?->leadUid;
    }

    public function getCurrent(): ?float
    {
        return $this->getBalance()?->current;
    }

    public function getDeposit(): ?float
    {
        return $this->getBalance()?->deposit;
    }

    public function getPotation(): ?float
    {
        return $this->getBalance()?->potation;
    }

    public function getActive(): ?float
    {
        return $this->getBalance()?->active;
    }
}
