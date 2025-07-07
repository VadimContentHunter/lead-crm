<?php

namespace crm\src\components\UserManagement\_common\interfaces;

use crm\src\components\UserManagement\CreateUser;
use crm\src\components\UserManagement\UpdateUser;
use crm\src\components\UserManagement\DeleteUser;
use crm\src\components\UserManagement\_common\interfaces\IGetUser;

interface IUserManagement
{
    /**
     * Доступ к созданию пользователя.
     */
    public function create(): CreateUser;

    /**
     * Доступ к получению пользователей (с обёрткой безопасности).
     */
    public function get(): IGetUser;

    /**
     * Доступ к обновлению пользователя.
     */
    public function update(): UpdateUser;

    /**
     * Доступ к удалению пользователя.
     */
    public function delete(): DeleteUser;
}
