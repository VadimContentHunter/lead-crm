<?php

namespace crm\src\components\SourcesManagement\common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\SourcesManagement\entities\Source;

interface ISourceResult extends IResult
{
    public function getSource(): ?Source;
}
