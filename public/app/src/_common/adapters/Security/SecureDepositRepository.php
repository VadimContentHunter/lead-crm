<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\DepositManagement\_entities\Deposit;
use crm\src\components\DepositManagement\_common\interfaces\IDepositRepository;

/**
 * Secure обёртка для DepositRepository с проверкой доступа.
 */
class SecureDepositRepository implements IDepositRepository
{
    private SecureWrapper $secure;

    public function __construct(
        IDepositRepository $repository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($repository, $accessGranter, $accessContext);
    }

    public function save(object|array $entity): ?int
    {
        return $this->secure->__call('save', [$entity]);
    }

    public function deleteByLeadId(int $leadId): ?int
    {
        return $this->secure->__call('deleteByLeadId', [$leadId]);
    }

    public function getByLeadId(int $leadId): ?Deposit
    {
        return $this->secure->__call('getByLeadId', [$leadId]);
    }

    public function updateByLeadId(Deposit $deposit): bool
    {
        return $this->secure->__call('updateByLeadId', [$deposit]);
    }

    public function update(object|array $entityOrData): ?int
    {
        return $this->secure->__call('update', [$entityOrData]);
    }

    public function deleteById(int $id): ?int
    {
        return $this->secure->__call('deleteById', [$id]);
    }

    public function getById(int $id): ?object
    {
        return $this->secure->__call('getById', [$id]);
    }

    public function getAll(): array
    {
        return $this->secure->__call('getAll', []);
    }

    public function getColumnNames(): array
    {
        return $this->secure->__call('getColumnNames', []);
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
