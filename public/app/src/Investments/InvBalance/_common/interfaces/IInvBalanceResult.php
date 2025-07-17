<?php

namespace crm\src\Investments\InvBalance\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvBalance\_entities\InvBalance;

/**
 * Результат операций с инвестиционным балансом.
 */
interface IInvBalanceResult extends IResult
{
    /**
     * @return InvBalance|null
     */
    public function getInvBalance(): ?InvBalance;

    /**
     * @return string|null
     */
    public function getLeadUid(): ?string;

    /**
     * @return float|null
     */
    public function getCurrent(): ?float;

    /**
     * @return float|null
     */
    public function getDeposit(): ?float;

    /**
     * @return float|null
     */
    public function getPotential(): ?float;

    /**
     * @return float|null
     */
    public function getActive(): ?float;
}
