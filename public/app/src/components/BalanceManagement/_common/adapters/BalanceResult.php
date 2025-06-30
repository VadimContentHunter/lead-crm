<?php

namespace crm\src\components\BalanceManagement\_common\adapters;

use Throwable;
use crm\src\_common\interfaces\AResult;
use crm\src\components\BalanceManagement\_entities\Balance;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceResult;

class BalanceResult extends AResult implements IBalanceResult
{
    public function getBalance(): ?Balance
    {
        return $this->data instanceof Balance ? $this->data : null;
    }

    public function getCurrent(): ?float
    {
        return $this->getBalance()?->current;
    }

    public function getDrain(): ?float
    {
        return $this->getBalance()?->drain;
    }

    public function getPotential(): ?float
    {
        return $this->getBalance()?->potential;
    }

    public function getLeadId(): ?int
    {
        return $this->getBalance()?->leadId;
    }

    public function getId(): ?int
    {
        return $this->getBalance()?->id;
    }
}
