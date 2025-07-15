<?php

namespace crm\src\Investments\Deposit\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\Deposit\_entities\InvDeposit;
use crm\src\Investments\Deposit\_common\InvDepositCollection;
use crm\src\Investments\Deposit\_common\interfaces\IDepositResult;

/**
 * Адаптер результата операций с депозитом.
 */
class DepositResult extends AResult implements IDepositResult
{
    public function getDeposit(): ?InvDeposit
    {
        return $this->data instanceof InvDeposit ? $this->data : null;
    }

    public function getUid(): ?string
    {
        return $this->getDeposit()?->uid;
    }

    public function getSum(): ?float
    {
        return $this->getDeposit()?->sum;
    }

    public function getId(): ?int
    {
        return $this->getDeposit()?->id;
    }

    public function getCollection(): InvDepositCollection
    {
        return $this->data instanceof InvDepositCollection
            ? $this->data
            : new InvDepositCollection([]);
    }
}
