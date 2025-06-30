<?php

namespace crm\src\components\UserManagement\common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\UserManagement\entities\User;

/**
 * @extends IRepository<User>
 */
interface IUserRepository extends IRepository
{
    /**
     * @return int|null Возвращает id удаленного пользователя
     */
    public function deleteByLogin(string $login): ?int;

    public function getByLogin(string $login): ?User;
}
