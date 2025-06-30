<?php

namespace crm\src\components\UserManagement\common\adapters;

use Throwable;
use crm\src\components\UserManagement\entities\User;
use crm\src\components\UserManagement\common\interfaces\IUserResult;

class UserResult implements IUserResult
{
    private function __construct(
        private mixed $data,
        private ?Throwable $error
    ) {
    }

    public static function success(mixed $data = null): self
    {
        return new self($data, null);
    }

    public static function failure(Throwable $error): self
    {
        return new self(null, $error);
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function getError(): ?Throwable
    {
        return $this->error;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getUser(): ?User
    {
        return $this->data instanceof User ? $this->data : null;
    }

    public function getInt(): ?int
    {
        return is_int($this->data) ? $this->data : null;
    }

    public function getBool(): ?bool
    {
        return is_bool($this->data) ? $this->data : null;
    }

    public function hasNull(): bool
    {
        return $this->data === null;
    }
}
