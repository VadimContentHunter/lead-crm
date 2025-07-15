<?php

namespace crm\src\Investments\InvActivity\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvActivity\_entities\InvInvActivity;
use crm\src\Investments\InvActivity\_common\InvInvActivityCollection;

/**
 * Результат операций с инвестиционной сделкой или их коллекцией.
 */
interface IInvActivityResult extends IResult
{
    /**
     * @return InvInvActivity|null
     */
    public function getInvActivity(): ?InvInvActivity;

    /**
     * @return InvInvActivityCollection|null
     */
    public function getCollection(): ?InvInvActivityCollection;

    /**
     * @return string|null
     */
    public function getHash(): ?string;

    /**
     * @return string|null
     */
    public function getLeadUid(): ?string;

    /**
     * @return float|null
     */
    public function getAmount(): ?float;

    /**
     * @return float|null
     */
    public function getResult(): ?float;

    /**
     * @return int|null
     */
    public function getId(): ?int;
}
