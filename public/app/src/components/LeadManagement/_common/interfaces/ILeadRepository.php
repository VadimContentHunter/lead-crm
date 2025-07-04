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
     * Удаляет лидов по accountManagerId
     *
     * @return int|null - число удалённых записей или null при ошибке
     */
    public function deleteByAccountManagerId(int $accountManagerId): ?int;

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
     * @return mixed[]
     */
    public function getFilteredLeads(LeadFilterDto $filter): array;
}
