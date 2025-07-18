<?php

namespace crm\src\Investments\InvLead\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\InvStatus\_entities\InvStatus;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;
use crm\src\Investments\InvLead\_common\DTOs\InvAccountManagerDto;
use crm\src\Investments\InvLead\_common\interfaces\IInvLeadResult;

/**
 * Адаптер результата операций с инвестиционным лидом.
 */
class InvLeadResult extends AResult implements IInvLeadResult
{
    public function getInvLead(): ?SimpleInvLead
    {
        return $this->data instanceof SimpleInvLead ? $this->data : null;
    }

    public function getDtoLead(): ?DbInvLeadDto
    {
        return $this->data instanceof DbInvLeadDto ? $this->data : null;
    }

    public function getUid(): ?string
    {
        return $this->getInvLead()?->uid;
    }

    public function getContact(): ?string
    {
        return $this->getInvLead()?->contact;
    }

    public function getPhone(): ?string
    {
        return $this->getInvLead()?->phone;
    }

    public function getEmail(): ?string
    {
        return $this->getInvLead()?->email;
    }

    public function getFullName(): ?string
    {
        return $this->getInvLead()?->fullName;
    }

    public function getAccountManager(): ?InvAccountManagerDto
    {
        return $this->getInvLead()?->accountManager;
    }

    public function getVisible(): ?bool
    {
        return $this->getInvLead()?->visible;
    }

    public function getSource(): ?InvSource
    {
        return $this->getInvLead()?->source;
    }

    public function getStatus(): ?InvStatus
    {
        return $this->getInvLead()?->status;
    }
}
