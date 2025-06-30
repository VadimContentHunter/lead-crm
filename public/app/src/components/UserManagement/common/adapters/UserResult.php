<?php

namespace crm\src\components\UserManagement\common\adapters;

use Throwable;
use crm\src\components\UserManagement\entities\User;
use crm\src\components\UserManagement\common\interfaces\IUserResult;

class UserResult implements IUserResult
{
    private function __construct(
        private ?User $user,
        private ?Throwable $error
    ) {
    }

    public static function success(User $user): self
    {
        return new self($user, null);
    }

    public static function failure(Throwable $error): self
    {
        return new self(null, $error);
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getError(): ?Throwable
    {
        return $this->error;
    }
}
