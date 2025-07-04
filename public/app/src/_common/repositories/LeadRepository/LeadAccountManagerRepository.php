<?php

namespace crm\src\_common\repositories\LeadRepository;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\LeadManagement\_common\DTOs\AccountManagerDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadAccountManagerRepository;
use crm\src\components\LeadManagement\_common\mappers\AccountManagerMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * @extends ARepository<AccountManagerDto>
 */
class LeadAccountManagerRepository extends ARepository implements ILeadAccountManagerRepository
{
    protected function getTableName(): string
    {
        return 'users';
    }

    /**
     * @return class-string<User>
     */
    protected function getEntityClass(): string
    {
        return User::class;
    }

    protected function fromArray(): callable
    {
        return [AccountManagerMapper::class, 'fromData'];
    }

    protected function toArray(object $entity): array
    {
        if ($entity instanceof User) {
            $dto = AccountManagerMapper::fromData($entity);
        } elseif ($entity instanceof AccountManagerDto) {
            $dto = $entity;
        } else {
            return []; // или throw new InvalidArgumentException();
        }

        if ($dto === null) {
            return [];
        }

        return AccountManagerMapper::toArray($dto);
    }

    public function deleteByLogin(string $login): ?int
    {
        $query = (new QueryBuilder())
            ->table($this->getTableName())
            ->where('login = :login')
            ->delete(['login' => $login]);

        $result = $this->repository->executeQuery($query);

        return $result->isSuccess() ? ($result->getInt() ?? null) : null;
    }

    public function getByLogin(string $login): ?AccountManagerDto
    {
        $query = (new QueryBuilder())
            ->table($this->getTableName())
            ->where('login = :login')
            ->select(['login' => $login]);

        $result = $this->repository->executeQuery($query);

        if (!$result->isSuccess()) {
            return null;
        }

        $user = $result->getObjectOrNull($this->getEntityClass());

        return $user instanceof User
            ? AccountManagerMapper::fromData($user)
            : null;
    }
}
