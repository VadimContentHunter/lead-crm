<?php

namespace crm\src\components\DepositManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\DepositManagement\_entities\Deposit;

/**
 * @extends IRepository<Deposit>
 */
interface IDepositRepository extends IRepository
{
    public function deleteByLeadId(int $leadId): ?int;

    public function getByLeadId(int $leadId): ?Deposit;
}
