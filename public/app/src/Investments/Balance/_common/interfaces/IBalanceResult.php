<?php

namespace crm\src\Investments\Balance\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\Balance\_entities\InvBalance;

/**
 * Результат операций с инвестиционным балансом.
 */
interface IBalanceResult extends IResult
{
    /**
     * @return InvBalance|null
     */
    public function getBalance(): ?InvBalance;

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
    public function getPotation(): ?float;

    /**
     * @return float|null
     */
    public function getActive(): ?float;
}
