<?php

namespace crm\src\Investments\Source\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\Investments\Source\_entities\InvSource;
use crm\src\Investments\Source\_common\interfaces\ISourceResult;
use crm\src\Investments\Source\_common\InvSourceCollection;

/**
 * Адаптер результата операций с инвестиционным источником.
 */
class SourceResult extends AResult implements ISourceResult
{
    /**
     * @inheritDoc
     */
    public function getSource(): ?InvSource
    {
        return $this->data instanceof InvSource ? $this->data : null;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): ?string
    {
        return $this->getSource()?->code;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(): ?string
    {
        return $this->getSource()?->label;
    }

    /**
     * @inheritDoc
     */
    public function getId(): ?int
    {
        return $this->getSource()?->id ?? null;
    }
}
