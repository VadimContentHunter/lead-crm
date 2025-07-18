<?php

namespace crm\src\Investments\InvLead\_common\interfaces;

use crm\src\_common\interfaces\IResultRepository;
use crm\src\Investments\InvLead\_common\DTOs\DbInvLeadDto;

/**
 * Интерфейс репозитория инвестиционных лидов.
 */
interface IInvAccountManagerRepository
{
    public function getById(int $id): ?object;

    /**
     * @return mixed[]
     */
    public function getAll(): array;
}
