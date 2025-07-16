<?php

namespace crm\src\Investments\InvDeposit\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvDeposit\_entities\InvDeposit;
use crm\src\Investments\InvDeposit\_common\InvDepositCollection;
use crm\src\Investments\InvDeposit\_common\interfaces\IInvDepositResult;

/**
 * Адаптер результата операций с депозитом.
 */
class InvDepositResult extends AResult implements IInvDepositResult
{
    public function getInvDeposit(): ?InvDeposit
    {
        return $this->data instanceof InvDeposit ? $this->data : null;
    }

    public function getUid(): ?string
    {
        return $this->getInvDeposit()?->uid;
    }

    public function getSum(): ?float
    {
        return $this->getInvDeposit()?->sum;
    }

    public function getId(): ?int
    {
        return $this->getInvDeposit()?->id;
    }

    public function getCollection(): InvDepositCollection
    {
        return $this->data instanceof InvDepositCollection
            ? $this->data
            : new InvDepositCollection([]);
    }
}
