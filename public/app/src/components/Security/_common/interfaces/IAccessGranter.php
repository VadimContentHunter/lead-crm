<?php

namespace crm\src\components\Security\_common\interfaces;

use crm\src\components\Security\_entities\AccessContext;

interface IAccessGranter
{
    /**
     * @param mixed[] $args
     */
    public function canCall(object $target, string $methodName, array $args, AccessContext $accessContext): bool;

    public function canCreate(string $className, ?AccessContext $accessContext): bool;
}
