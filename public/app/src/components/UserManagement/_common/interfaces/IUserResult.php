<?php

namespace crm\src\components\UserManagement\_common\interfaces;

use crm\src\_common\interfaces\IResult;
use crm\src\components\UserManagement\_entities\User;

interface IUserResult extends IResult
{
    public function getUser(): ?User;

    public function getId(): ?int;

    public function getLogin(): ?string;

    public function getPasswordHash(): ?string;
}
