<?php

namespace crm\src\components\SourceManagement\_common\adapters;

use Throwable;
use crm\src\_common\interfaces\AResult;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\SourceManagement\_common\interfaces\ISourceResult;

class SourceResult extends AResult implements ISourceResult
{
    public function getSource(): ?Source
    {
        return $this->data instanceof Source ? $this->data : null;
    }

    public function getId(): ?int
    {
        return $this->getSource()?->id;
    }

    public function getTitle(): ?string
    {
        return $this->getSource()?->title;
    }
}
