<?php

namespace crm\src\Investments\InvSource\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\InvSource\_entities\InvSource;
use crm\src\Investments\InvSource\_common\interfaces\IInvSourceResult;
use crm\src\Investments\InvSource\_common\InvSourceCollection;

/**
 * Адаптер результата операций с инвестиционным источником.
 */
class InvSourceResult extends AResult implements IInvSourceResult
{
    /**
     * @inheritDoc
     */
    public function getInvSource(): ?InvSource
    {
        return $this->data instanceof InvSource ? $this->data : null;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): ?string
    {
        return $this->getInvSource()?->code;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): ?string
    {
        return $this->getInvSource()?->label;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->getInvSource()?->id ?? null;
    }
}
