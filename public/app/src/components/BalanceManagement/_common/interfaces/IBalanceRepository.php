<?php

namespace crm\src\components\BalanceManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\BalanceManagement\_entities\Balance;

/**
 * @extends IRepository<Balance>
 */
interface IBalanceRepository extends IRepository
{
    /**
     * @return int|null Возвращает id удаленного источника по названию
     */
    public function deleteByLeadId(int $leadId): ?int;

    /**
     * @return Balance|null
     */
    public function getLeadId(int $leadId): ?Balance;
}
