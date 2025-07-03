<?php

namespace crm\src\_common\repositories;

use crm\src\_common\repositories\ARepository;
use crm\src\components\UserManagement\_entities\User;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;

/**
 * @extends ARepository<User>
 */
class UserRepository extends ARepository implements IUserRepository
{
    protected function getTableName(): string
    {
        return 'users';
    }

    protected function getEntityClass(): string
    {
        return User::class;
    }

    protected function fromArray(): callable
    {
        return [UserMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var User $entity
*/
        return UserMapper::toArray($entity);
    }

    public function deleteByLogin(string $login): ?int
    {
        return $this->repository->executeQuery(
            (new \crm\src\services\Repositories\QueryBuilder\QueryBuilder())
                ->table($this->getTableName())
                ->where('login = :login')
                ->delete(['login' => $login])
        )->getInt();
    }

    public function getByLogin(string $login): ?User
    {
        return $this->repository->executeQuery(
            (new \crm\src\services\Repositories\QueryBuilder\QueryBuilder())
                ->table($this->getTableName())
                ->where('login = :login')
                ->select()
        )->getObjectOrNull($this->getEntityClass());
    }
}
