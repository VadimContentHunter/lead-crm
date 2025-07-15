<?php

namespace crm\src\Investments\Status\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\Status\_entities\InvStatus;
use crm\src\Investments\Status\_common\interfaces\IStatusResult;

/**
 * Адаптер результата операций со статусом инвестиции.
 */
class StatusResult extends AResult implements IStatusResult
{
    /**
     * @inheritDoc
     */
    public function getStatus(): ?InvStatus
    {
        return $this->data instanceof InvStatus ? $this->data : null;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): ?string
    {
        return $this->getStatus()?->code;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): ?string
    {
        return $this->getStatus()?->label;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->getStatus()?->id ?? null;
    }
}
