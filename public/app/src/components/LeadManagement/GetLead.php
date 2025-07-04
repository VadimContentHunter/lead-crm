<?php

namespace crm\src\components\LeadManagement;

use Throwable;
use crm\src\components\LeadManagement\_common\DTOs\LeadFilterDto;
use crm\src\components\LeadManagement\_common\adapters\LeadResult;
use crm\src\components\LeadManagement\_common\interfaces\ILeadResult;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;
use crm\src\components\LeadManagement\_exceptions\LeadManagementException;
use crm\src\components\LeadManagement\_common\interfaces\ILeadSourceRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadStatusRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadAccountManagerRepository;

class GetLead
{
    public function __construct(
        private ILeadRepository $repository,
        private ILeadSourceRepository $sourceRepository,
        private ILeadStatusRepository $statusRepository,
        private ILeadAccountManagerRepository $accManagerRepository,
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

    /**
     * Возвращает названия столбцов таблицы пользователей.
     *
     * @param  array<string, string> $renameMap Ключ — оригинальное имя, значение — новое имя
     * @return IUserResult
     */
    public function executeColumnNames(array $renameMap = []): ILeadResult
    {
        try {
            $columns = $this->repository->getColumnNames();

            if (!empty($renameMap)) {
                $columns = array_map(
                    fn($name) => $renameMap[$name] ?? $name,
                    $columns
                );
            }

            return LeadResult::success($columns);
        } catch (\Throwable $e) {
            return LeadResult::failure($e);
        }
    }

    /**
     * Получает все статусы с применением маппера к каждому элементу.
     *
     * @template T
     * @param    callable(Status): T $mapper
     * @return   ISourceResult
     */
    public function executeAllMapped(callable $mapper): ILeadResult
    {
        try {
            $leads = $this->repository->getAll();
            $hydratedLeads = $this->hydrateLeads($leads);
            $mapped = array_map($mapper, $hydratedLeads);

            return LeadResult::success($mapped);
        } catch (Throwable $e) {
            return LeadResult::failure($e);
        }
    }


    public function all(): ILeadResult
    {
        try {
            $leads = $this->repository->getAll();
            if (empty($leads)) {
                return LeadResult::failure(new LeadManagementException("Лиды не найдены"));
            }

            $hydratedLeads = $this->hydrateLeads($leads);
            return LeadResult::success($hydratedLeads);
        } catch (Throwable $e) {
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

    /**
     * Наполняет сущности Lead связными данными (source, status, accountManager).
     *
     * @param  Lead[] $leads
     * @return Lead[]
     */
    public function hydrateLeads(array $leads): array
    {
        foreach ($leads as $lead) {
            if ($lead->source?->id) {
                $lead->source = $this->sourceRepository->getById($lead->source->id);
            }

            if ($lead->status?->id) {
                $lead->status = $this->statusRepository->getById($lead->status->id);
            }

            if ($lead->accountManager?->id) {
                $lead->accountManager = $this->accManagerRepository->getById($lead->accountManager->id);
            }
        }

        return $leads;
    }
}
