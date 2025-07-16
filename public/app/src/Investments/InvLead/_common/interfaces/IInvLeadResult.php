<?php

namespace crm\src\Investments\InvLead\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\InvStatus\_entities\InvStatus;
use crm\src\Investments\InvLead\_entities\SimpleInvLead;
use crm\src\Investments\InvLead\_common\DTOs\InvAccountManagerDto;

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

    public function getContact(): ?string;
    public function getPhone(): ?string;
    public function getEmail(): ?string;
    public function getFullName(): ?string;
    public function getAccountManager(): ?InvAccountManagerDto;
    public function getVisible(): ?bool;
    public function getSource(): ?InvSource;
    public function getStatus(): ?InvStatus;
}
