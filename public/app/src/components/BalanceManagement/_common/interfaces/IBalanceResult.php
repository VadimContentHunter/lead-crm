<?php

namespace crm\src\components\BalanceManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\BalanceManagement\_entities\Balance;

interface IBalanceResult extends IResult
{
    public function getBalance(): ?Balance;

    public function getCurrent(): ?float;

    public function getDrain(): ?float;

    public function getPotential(): ?float;

    public function getLeadId(): ?int;

    public function getId(): ?int;
}
