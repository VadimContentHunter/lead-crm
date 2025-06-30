<?php

namespace crm\src\components\LeadManagement\_common\adapters;

use DateTime;
use crm\src\_common\interfaces\AResult;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\interfaces\ILeadResult;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\DTOs\UserDto;

class LeadResult extends AResult implements ILeadResult
{
    public function getLead(): ?Lead
    {
        return $this->data instanceof Lead ? $this->data : null;
    }

    public function getId(): ?int
    {
        return $this->getLead()?->id;
    }

    public function getFullName(): ?string
    {
        return $this->getLead()?->fullName;
    }

    public function getContact(): ?string
    {
        return $this->getLead()?->contact;
    }

    public function getAddress(): ?string
    {
        return $this->getLead()?->address;
    }

    public function getSource(): ?SourceDto
    {
        return $this->getLead()?->source;
    }

    public function getSourceTitle(): ?string
    {
        return $this->getSource()?->title ?? null;
    }

    public function getSourceId(): ?int
    {
        return $this->getSource()?->id;
    }

    public function getStatus(): ?StatusDto
    {
        return $this->getLead()?->status;
    }

    public function getStatusTitle(): ?string
    {
        return $this->getStatus()?->title ?? null;
    }

    public function getStatusId(): ?int
    {
        return $this->getStatus()?->id;
    }

    public function getAccountManager(): ?UserDto
    {
        return $this->getLead()?->accountManager;
    }

    public function getAccountManagerLogin(): ?string
    {
        return $this->getAccountManager()?->login ?? null;
    }

    public function getAccountManagerId(): ?int
    {
        return $this->getAccountManager()?->id;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->getLead()?->createdAt;
    }
}
