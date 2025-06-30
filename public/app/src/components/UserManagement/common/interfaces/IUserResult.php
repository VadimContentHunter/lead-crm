<?php

namespace crm\src\components\UserManagement\common\interfaces;

use Throwable;
use crm\src\_common\interfaces\IResult;
use crm\src\components\UserManagement\entities\User;

interface IUserResult extends IResult
{
    public function getUser(): ?User;
}
