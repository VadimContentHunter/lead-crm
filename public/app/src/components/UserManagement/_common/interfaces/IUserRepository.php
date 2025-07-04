<?php

namespace crm\src\components\UserManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;

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

    /**
     * @param  UserFilterDto $filter
     * @return mixed[]
     */
    public function getFilteredUsers(UserFilterDto $filter): array;
}
