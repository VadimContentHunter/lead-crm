<?php

namespace crm\src\components\Security;

use crm\src\components\UserManagement\_entities\User;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_exceptions\SecurityException;
use crm\src\components\Security\_common\interfaces\IAccessGranter;

class SecureWrapper
{
    public function __construct(
        private object $target,
        private IAccessGranter $accessGranter,
        private AccessContext $accessContext
    ) {
    }

    /**
     * @param  mixed $args
     * @return void
     */
    public function __call(string $method, array $args)
    {
        if (!method_exists($this->target, $method)) {
            throw new SecurityException("Метод $method не существует в целевом объекте.");
        }

        if (!$this->accessGranter->canCall($this->target, $method, $args, $this->accessContext)) {
            throw new SecurityException("Доступ к методу $method запрещен для пользователя {$this->accessContext->userId}");
        }

        return $this->target->$method(...$args);
    }
}
