<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\components\Security\_entities\AccessContext;

interface IAccessGranter
{
    public function canCall(object $target, string $methodName, array $args, AccessContext $accessContext): bool;
}
