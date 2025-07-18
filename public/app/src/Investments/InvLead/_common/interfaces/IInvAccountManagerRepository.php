<?php

namespace crm\src\Investments\InvLead\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;

/**
 * Интерфейс репозитория инвестиционных лидов.
 *
 * @extends IResultRepository<DbInvLeadDto>
 */
interface IInvAccountManagerRepository extends IRepository
{
}
