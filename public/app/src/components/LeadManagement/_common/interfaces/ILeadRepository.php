<?php

namespace crm\src\components\LeadManagement\_common\interfaces;

use crm\src\_common\interfaces\IRepository;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\LeadFilterDto;

/**
 * @extends IRepository<Lead>
 */
interface ILeadRepository extends IRepository
{
    /**
     * Получить лиды, закреплённые за менеджером по его ID.
     *
     * @param  int $managerId
     * @return Lead[]
     */
    public function getLeadsByManagerId(int $managerId): array;

    /**
     * Получить лиды по ID источника.
     *
     * @param  int $sourceId
     * @return Lead[]
     */
    public function getLeadsBySourceId(int $sourceId): array;

    /**
     * Получить лиды по ID статуса.
     *
     * @param  int $statusId
     * @return Lead[]
     */
    public function getLeadsByStatusId(int $statusId): array;

    /**
     * Получить лиды с фильтрацией по различным параметрам.
     *
     * @param  LeadFilterDto $filter
     * @param  string $sortBy
     * @param  string $sortDir
     * @return Lead[]
     */
    public function getFilteredLeads(LeadFilterDto $filter, string $sortBy = 'leads.id', string $sortDir = 'asc'): array;
}
