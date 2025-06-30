<?php

namespace crm\src\components\SourceManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\SourceManagement\_entities\Source;

interface ISourceResult extends IResult
{
    public function getSource(): ?Source;
}
