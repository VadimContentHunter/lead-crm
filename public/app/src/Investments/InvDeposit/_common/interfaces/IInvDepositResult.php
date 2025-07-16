<?php

namespace crm\src\Investments\InvDeposit\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvDeposit\_entities\InvDeposit;
use crm\src\Investments\InvDeposit\_common\InvDepositCollection;

/**
 * Результат операций с инвестиционным депозитом или их коллекцией.
 */
interface IInvDepositResult extends IResult
{
    /**
     * @return InvDeposit|null
     */
    public function getInvDeposit(): ?InvDeposit;

    /**
     * @return InvDepositCollection|null
     */
    public function getCollection(): ?InvDepositCollection;

    /**
     * @return string|null
     */
    public function getUid(): ?string;

    /**
     * @return float|null
     */
    public function getSum(): ?float;

    /**
     * @return int|null
     */
    public function getId(): ?int;
}
