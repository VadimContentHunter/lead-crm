<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;
use crm\src\Investments\InvLead\_common\interfaces\IInvAccountManagerRepository;
use crm\src\components\LeadManagement\_common\interfaces\ILeadAccountManagerRepository;

/**
 * Secure обёртка над UserRepository с проверкой доступа.
 */
class SecureUserRepository implements IUserRepository, IInvAccountManagerRepository
{
    private SecureWrapper $secure;

    /**
     * @param IUserRepository $repository
     */
    public function __construct(
        IUserRepository $repository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($repository, $accessGranter, $accessContext);
    }

    public function deleteByLogin(string $login): ?int
    {
        return $this->secure->__call('deleteByLogin', [$login]);
    }

    public function getByLogin(string $login): ?User
    {
        return $this->secure->__call('getByLogin', [$login]);
    }

    public function getFilteredUsers(UserFilterDto $filter): array
    {
        return $this->secure->__call('getFilteredUsers', [$filter]);
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
