<?php

namespace crm\src\components\LeadManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use crm\src\components\LeadManagement\_entities\Lead;

interface ILeadResult extends IResult
{
    public function getLead(): ?Lead;

    public function getId(): ?int;

    public function getFullName(): ?string;

    public function getContact(): ?string;

    public function getAddress(): ?string;

    // === Методы для Source ===

    /**
     * @return SourceDto|null
     */
    public function getSource(): ?SourceDto;
    public function getSourceTitle(): ?string;
    public function getSourceId(): ?int;

    // === Методы для Status ===

    /**
     * @return StatusDto|null
     */
    public function getStatus(): ?StatusDto;
    public function getStatusTitle(): ?string;
    public function getStatusId(): ?int;

    // === Методы для User (Account Manager) ===

    /**
     * @return AccountManagerDto|null
     */
    public function getAccountManager(): ?AccountManagerDto;
    public function getAccountManagerLogin(): ?string;
    public function getAccountManagerId(): ?int;

    public function getCreatedAt(): ?\DateTime;
}
