<?php

namespace crm\src\components\LeadManagement;

use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\LeadManagement\_common\DTOs\LeadFilterDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadResult;
use crm\src\components\LeadManagement\_common\adapters\LeadResult;
use crm\src\components\LeadManagement\_exceptions\LeadManagementException;

class GetLead
{
    public function __construct(
        private ILeadRepository $repository,
    ) {
    }

    public function byId(int $id): ILeadResult
    {
        try {
            $lead = $this->repository->getById($id);
            if ($lead === null) {
                return LeadResult::failure(new LeadManagementException("Лид с ID $id не найден"));
            }
            return LeadResult::success($lead);
        } catch (\Throwable $e) {
            return LeadResult::failure($e);
        }
    }

    public function byManagerId(int $managerId): ILeadResult
    {
        try {
            $leads = $this->repository->getLeadsByManagerId($managerId);
            if (empty($leads)) {
                return LeadResult::failure(new LeadManagementException("Лиды менеджера с ID $managerId не найдены"));
            }
            return LeadResult::success($leads);
        } catch (\Throwable $e) {
            return LeadResult::failure($e);
        }
    }

    public function bySourceId(int $sourceId): ILeadResult
    {
        try {
            $leads = $this->repository->getLeadsBySourceId($sourceId);
            if (empty($leads)) {
                return LeadResult::failure(new LeadManagementException("Лиды источника с ID $sourceId не найдены"));
            }
            return LeadResult::success($leads);
        } catch (\Throwable $e) {
            return LeadResult::failure($e);
        }
    }

    public function byStatusId(int $statusId): ILeadResult
    {
        try {
            $leads = $this->repository->getLeadsByStatusId($statusId);
            if (empty($leads)) {
                return LeadResult::failure(new LeadManagementException("Лиды со статусом ID $statusId не найдены"));
            }
            return LeadResult::success($leads);
        } catch (\Throwable $e) {
            return LeadResult::failure($e);
        }
    }

    public function filtered(LeadFilterDto $filter, string $sortBy = 'leads.id', string $sortDir = 'asc'): ILeadResult
    {
        try {
            $leads = $this->repository->getFilteredLeads($filter, $sortBy, $sortDir);
            if (empty($leads)) {
                return LeadResult::failure(new LeadManagementException("Лиды по фильтру не найдены"));
            }
            return LeadResult::success($leads);
        } catch (\Throwable $e) {
            return LeadResult::failure($e);
        }
    }
}
