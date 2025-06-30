<?php

namespace crm\src\components\SourcesManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\SourcesManagement\_entities\Source;

interface ISourceResult extends IResult
{
    public function getSource(): ?Source;
}
