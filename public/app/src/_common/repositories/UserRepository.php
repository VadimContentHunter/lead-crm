<?php

namespace crm\src\_common\repositories;

use PDO;
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;
use crm\src\components\UserManagement\_entities\User;
use crm\src\services\Repositories\DbRepository\DbRepository;
use crm\src\components\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\UserManagement\_common\mappers\UserMapper;
use crm\src\components\UserManagement\_common\interfaces\IUserRepository;

class UserRepository implements IUserRepository
{
    private DbRepository $repository;

    public function __construct(
        PDO $pdo,
        private LoggerInterface $logger = new NullLogger()
    ) {
        $this->repository = new DbRepository($pdo);
    }

    public function deleteByLogin(string $login): ?int
    {
        return $this->repository->executeQuery((new QueryBuilder())
                ->table('users')
                ->where('login = :login')
                ->delete(['login' => $login]))
                    ->getInt();
    }

    public function getByLogin(string $login): ?User
    {
        return $this->repository->executeQuery((new QueryBuilder())
                ->table('users')
                ->where('login = :login')
                ->select(['login']))
                    ->getObjectOrNull(User::class);
    }

    /**
     * @param User $user
     */
    public function save(object $user): ?int
    {
        return $this->repository->executeQuery((new QueryBuilder())
                ->table('users')
                ->insert(['login' => $user->login, 'password_hash' => $user->passwordHash]))
                    ->getInt();
    }

    /**
     * @param User $entity
     */
    public function update(object $entity): ?int
    {
        return $this->repository->executeQuery((new QueryBuilder())
                ->table('users')
                ->where('login = :login')
                ->update(['login' => $entity->login, 'password_hash' => $entity->passwordHash]))
                    ->getInt();
    }

    public function deleteById(int $id): ?int
    {
        return $this->repository->executeQuery((new QueryBuilder())
                ->table('users')
                ->where('id = :id')
                ->delete(['id' => $id]))
                    ->getInt();
    }

    public function getById(int $id): ?User
    {
        return $this->repository->executeQuery((new QueryBuilder())
                ->table('users')
                ->where('id = :id')
                ->select(['id', 'login', 'password_hash']))
                    ->getObjectOrNull(User::class);
    }

    public function getAll(): array
    {
        return $this->repository->executeQuery((new QueryBuilder())
                ->table('users')
                ->select(['id', 'login', 'password_hash']))
                    ->getValidMappedList([UserMapper::class, 'fromArray']);
    }
}
