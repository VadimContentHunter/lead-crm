<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\BalanceManagement\_entities\Balance;
use crm\src\components\BalanceManagement\_common\interfaces\IBalanceRepository;

/**
 * Secure обёртка для BalanceRepository с проверкой доступа.
 */
class SecureBalanceRepository implements IBalanceRepository
{
    private SecureWrapper $secure;

    public function __construct(
        IBalanceRepository $repository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($repository, $accessGranter, $accessContext);
    }

    public function deleteByLeadId(int $leadId): ?int
    {
        return $this->secure->__call('deleteByLeadId', [$leadId]);
    }

    public function getByLeadId(int $leadId): ?Balance
    {
        return $this->secure->__call('getByLeadId', [$leadId]);
    }

    public function updateByLeadId(Balance $balance): bool
    {
        return $this->secure->__call('updateByLeadId', [$balance]);
    }

    public function save(object|array $entityOrData): ?int
    {
        return $this->secure->__call('save', [$entityOrData]);
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
