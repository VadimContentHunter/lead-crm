<?php

namespace crm\src\Investments\InvActivity\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvActivity\_entities\InvActivity;
use crm\src\Investments\InvActivity\_common\InvActivityCollection;

/**
 * Результат операций с инвестиционной сделкой или их коллекцией.
 */
interface IInvActivityResult extends IResult
{
    /**
     * @return InvActivity|null
     */
    public function getInvActivity(): ?InvActivity;

    /**
     * @return InvActivityCollection|null
     */
    public function getCollection(): ?InvActivityCollection;

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
