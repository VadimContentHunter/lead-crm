<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\LeadManagement\_entities\Lead;
use crm\src\components\LeadManagement\_common\DTOs\LeadFilterDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadRepository;

/**
 * Secure обёртка над LeadRepository с проверкой доступа.
 */
class SecureLeadRepository implements ILeadRepository
{
    private SecureWrapper $secure;

    public function __construct(
        ILeadRepository $repository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($repository, $accessGranter, $accessContext);
    }

    public function getColumnNames(): array
    {
        return $this->secure->__call('getColumnNames', []);
    }

    public function save(object|array $entity): ?int
    {
        return $this->secure->__call('save', [$entity]);
    }

    public function update(object|array $entityOrData): ?int
    {
        return $this->secure->__call('update', [$entityOrData]);
    }

    public function deleteById(int $id): ?int
    {
        return $this->secure->__call('deleteById', [$id]);
    }

    public function getById(int $id): ?Lead
    {
        return $this->secure->__call('getById', [$id]);
    }

    public function getAll(): array
    {
        return $this->secure->__call('getAll', []);
    }

    public function deleteByAccountManagerId(int $accountManagerId): ?int
    {
        return $this->secure->__call('deleteByAccountManagerId', [$accountManagerId]);
    }

    public function getLeadsByManagerId(int $managerId): array
    {
        return $this->secure->__call('getLeadsByManagerId', [$managerId]);
    }

    public function getLeadsBySourceId(int $sourceId): array
    {
        return $this->secure->__call('getLeadsBySourceId', [$sourceId]);
    }

    public function getLeadsByStatusId(int $statusId): array
    {
        return $this->secure->__call('getLeadsByStatusId', [$statusId]);
    }

    public function getFilteredLeads(LeadFilterDto $filter): array
    {
        return $this->secure->__call('getFilteredLeads', [$filter]);
    }

    public function getAllExcept(string $column = '', array $excludedValues = []): array
    {
        return $this->secure->__call('getAllExcept', [$column, $excludedValues]);
    }

    public function getAllByColumnValues(string $column = '', array $values = []): array
    {
        return $this->secure->__call('getAllByColumnValues', [$column, $values]);
    }
}
