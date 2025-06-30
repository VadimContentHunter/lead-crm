<?php

namespace crm\src\components\UserManagement\_common\adapters;

use Throwable;
use crm\src\_common\interfaces\AResult;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\interfaces\IUserResult;

class UserResult extends AResult implements IUserResult
{
    public function getUser(): ?User
    {
        return $this->data instanceof User ? $this->data : null;
    }

    public function getId(): ?int
    {
        return $this->getUser()?->id;
    }

    public function getLogin(): ?string
    {
        return $this->getUser()?->login;
    }

    public function getPasswordHash(): ?string
    {
        return $this->getUser()?->passwordHash;
    }
}
