<?php

namespace crm\src\Investments\InvStatus\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvStatus\_entities\InvStatus;
use crm\src\Investments\InvStatus\_common\interfaces\IInvStatusResult;

/**
 * Адаптер результата операций со статусом инвестиции.
 */
class InvStatusResult extends AResult implements IInvStatusResult
{
    /**
     * @inheritDoc
     */
    public function getInvStatus(): ?InvStatus
    {
        return $this->data instanceof InvStatus ? $this->data : null;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): ?string
    {
        return $this->getInvStatus()?->code;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): ?string
    {
        return $this->getInvStatus()?->label;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->getInvStatus()?->id ?? null;
    }
}
