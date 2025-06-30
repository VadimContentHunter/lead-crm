<?php

namespace crm\src\components\UserManagement\common\interfaces;

use crm\src\components\UserManagement\entities\User;
use Throwable;

interface IUserResult
{
    public function isSuccess(): bool;

    public function getUser(): ?User;

    public function getInt(): ?int;

    public function getBool(): ?bool;

    public function getData(): mixed;

    public function hasNull(): bool;

    public function getError(): ?Throwable;
}
