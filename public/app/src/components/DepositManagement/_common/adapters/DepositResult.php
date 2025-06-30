<?php

namespace crm\src\components\DepositManagement\_common\adapters;

use Throwable;
use crm\src\_common\interfaces\AResult;
use crm\src\components\DepositManagement\_entities\Deposit;
use crm\src\components\DepositManagement\_common\interfaces\IDepositResult;

class DepositResult extends AResult implements IDepositResult
{
    public function getDeposit(): ?Deposit
    {
        return $this->data instanceof Deposit ? $this->data : null;
    }

    public function getId(): ?int
    {
        return $this->getDeposit()?->id;
    }

    public function getLeadId(): ?int
    {
        return $this->getDeposit()?->leadId;
    }

    public function getSum(): ?float
    {
        return $this->getDeposit()?->sum;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->getDeposit()?->createdAt;
    }

    public static function success(mixed $data = null): static
    {
        return new static($data, null);
    }

    public static function failure(Throwable $error): static
    {
        return new static(null, $error);
    }
}
