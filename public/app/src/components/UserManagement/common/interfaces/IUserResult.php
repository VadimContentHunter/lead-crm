<?php

namespace crm\src\components\UserManagement\common\interfaces;

use crm\src\components\UserManagement\entities\User;
use Throwable;

interface IUserResult
{
    public function isSuccess(): bool;

    public function getUser(): ?User;

    public function getError(): ?Throwable;
}
