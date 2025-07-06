<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\UserManagement\_entities\User;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\UserManagement\_common\DTOs\UserFilterDto;
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

    /**
     * @return class-string<User>
     */
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
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('login = :login')
                ->delete(['login' => $login])
        )->getInt();
    }

    public function getByLogin(string $login): ?User
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('login = :login')
                ->bindings(['login' => $login])
                ->select()
        )->first()->getObjectOrNullWithMapper($this->getEntityClass(), $this->fromArray());
    }

    /**
     * @param  UserFilterDto $filter
     * @return mixed[]
     */
    public function getFilteredUsers(UserFilterDto $filter): array
    {
        $params = [];

        $sql = <<<SQL
            SELECT users.*
            FROM users
            WHERE 1 = 1
        SQL;

        // Фильтр по search (ищем в login и id как строке)
        if (!empty($filter->search)) {
            if (ctype_digit($filter->search)) {
                $sql .= " AND (users.id = :searchId OR users.login LIKE :searchLogin)";
                $params['searchId'] = (int)$filter->search;
                $params['searchLogin'] = '%' . $filter->search . '%';
            } else {
                $sql .= " AND users.login LIKE :searchLogin";
                $params['searchLogin'] = '%' . $filter->search . '%';
            }
        }

        // Фильтр по login (строгое сравнение)
        if (!empty($filter->login)) {
            $sql .= " AND users.login = :login";
            $params['login'] = $filter->login;
        }

        // Доступные поля сортировки
        $allowedSortFields = ['users.id', 'users.login', 'users.created_at'];

        // Получаем параметры сортировки из фильтра
        $sortBy = in_array($filter->sort, $allowedSortFields, true) ? $filter->sort : 'users.id';
        $sortDir = strtolower($filter->sortDir ?? '') === 'desc' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY {$sortBy} {$sortDir}";

        $result = $this->repository->executeSql($sql, $params);

        return $result->getArrayOrNull() ?? [];
    }
}
