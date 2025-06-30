<?php

namespace crm\src\components\StatusManagement\_common\adapters;

use crm\src\_common\interfaces\AResult;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\interfaces\IStatusResult;

class StatusResult extends AResult implements IStatusResult
{
    public function getStatus(): ?Status
    {
        return $this->data instanceof Status ? $this->data : null;
    }

    public function getId(): ?int
    {
        return $this->getStatus()?->id;
    }

    public function getTitle(): ?string
    {
        return $this->getStatus()?->title;
    }
}
