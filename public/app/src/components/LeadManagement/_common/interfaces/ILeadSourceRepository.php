<?php

namespace crm\src\components\LeadManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;

/**
 * @extends IRepository<SourceDto>
 */
interface ILeadSourceRepository extends IRepository
{
    /**
     * Получить источник по названию.
     *
     * @param  string $title
     * @return SourceDto|null
     */
    public function getByTitle(string $title): ?SourceDto;

    /**
     * Удалить источник по названию.
     *
     * @param  string $title
     * @return int|null
     */
    public function deleteByTitle(string $title): ?int;
}
