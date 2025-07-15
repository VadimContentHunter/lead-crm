<?php

namespace crm\src\Investments\Lead\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\Lead\_entities\SimpleInvLead;

/**
 * Результат операций с инвестиционным лидом или их коллекцией.
 */
interface IInvLeadResult extends IResult
{
    /**
     * @return SimpleInvLead|null
     */
    public function getLead(): ?SimpleInvLead;

    /**
     * @return string|null
     */
    public function getUid(): ?string;
}
