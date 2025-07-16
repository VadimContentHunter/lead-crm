<?php

namespace crm\src\Investments\InvLead\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;

/**
 * Адаптер результата операций с инвестиционным лидом.
 */
class InvLeadResult extends AResult implements IInvLeadResult
{
    /**
     * @inheritDoc
     */
    public function getInvLead(): ?SimpleInvLead
    {
        return $this->data instanceof SimpleInvLead ? $this->data : null;
    }

    /**
     * @inheritDoc
     */
    public function getUid(): ?string
    {
        return $this->getInvLead()?->uid;
    }
}
