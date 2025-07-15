<?php

namespace crm\src\Investments\Deposit\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\Deposit\_entities\InvDeposit;
use crm\src\Investments\Deposit\_common\InvDepositCollection;

/**
 * Результат операций с инвестиционным депозитом или их коллекцией.
 */
interface IDepositResult extends IResult
{
    /**
     * @return InvDeposit|null
     */
    public function getDeposit(): ?InvDeposit;

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
