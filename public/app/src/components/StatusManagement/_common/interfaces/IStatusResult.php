<?php

namespace crm\src\components\StatusesManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\StatusesManagement\_entities\Status;

interface IStatusResult extends IResult
{
    public function getStatus(): ?Status;
}
