<?php

namespace crm\src\components\LeadManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;

/**
 * @extends IRepository<StatusDto>
 */
interface ILeadStatusRepository extends IRepository
{
    /**
     * Получает статус по названию.
     *
     * @param  string $title
     * @return StatusDto|null
     */
    public function getByTitle(string $title): ?StatusDto;

    /**
     * Удаляет статус по названию.
     *
     * @param  string $title
     * @return int|null
     */
    public function deleteByTitle(string $title): ?int;
}
