<?php

namespace crm\src\components\StatusManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\StatusManagement\_entities\Status;

interface IStatusResult extends IResult
{
    public function getStatus(): ?Status;

    public function getId(): ?int;

    public function getTitle(): ?string;
}
