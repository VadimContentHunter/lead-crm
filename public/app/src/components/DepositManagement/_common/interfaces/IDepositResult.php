<?php

namespace crm\src\components\DepositManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\DepositManagement\_entities\Deposit;

interface IDepositResult extends IResult
{
    public function getDeposit(): ?Deposit;

    public function getId(): ?int;

    public function getLeadId(): ?int;

    public function getSum(): ?float;

    public function getTxId(): ?string;

    public function getCreatedAt(): ?\DateTime;
}
