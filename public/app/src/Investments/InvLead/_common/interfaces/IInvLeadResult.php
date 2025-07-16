<?php

namespace crm\src\Investments\InvLead\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;

/**
 * Результат операций с инвестиционным лидом или их коллекцией.
 */
interface IInvLeadResult extends IResult
{
    /**
     * @return SimpleInvLead|null
     */
    public function getInvLead(): ?SimpleInvLead;

    /**
     * @return string|null
     */
    public function getUid(): ?string;
}
