<?php

namespace crm\src\components\LeadManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;

/**
 * @extends IRepository<AccountManagerDto>
 */
interface ILeadAccountManagerRepository extends IRepository
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
     * @return AccountManagerDto|null
     */
    public function getByLogin(string $login): ?AccountManagerDto;
}
