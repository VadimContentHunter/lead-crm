<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadAccountManagerRepository;

/**
 * Secure обёртка для LeadAccountManagerRepository с проверкой доступа.
 */
class SecureLeadAccountManagerRepository implements ILeadAccountManagerRepository
{
    private SecureWrapper $secure;

    public function __construct(
        ILeadAccountManagerRepository $repository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($repository, $accessGranter, $accessContext);
    }

    public function deleteByLogin(string $login): ?int
    {
        return $this->secure->__call('deleteByLogin', [$login]);
    }

    public function getByLogin(string $login): ?AccountManagerDto
    {
        return $this->secure->__call('getByLogin', [$login]);
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
