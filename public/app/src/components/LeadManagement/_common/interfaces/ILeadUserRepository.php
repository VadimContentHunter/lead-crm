<?php

namespace crm\src\components\LeadManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\LeadManagement\_common\DTOs\UserDto;

/**
 * @extends IRepository<UserDto>
 */
interface ILeadUserRepository extends IRepository
{
    /**
     * Удаляет пользователя по логину.
     *
     * @param  string $login
     * @return int|null
     */
    public function deleteByLogin(string $login): ?int;

    /**
     * Получает пользователя по логину.
     *
     * @param  string $login
     * @return UserDto|null
     */
    public function getByLogin(string $login): ?UserDto;
}
