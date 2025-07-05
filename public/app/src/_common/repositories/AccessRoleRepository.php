<?php

namespace crm\src\components\Security\_repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\Security\_common\interfaces\IAccessRoleRepository;
use crm\src\components\Security\_entities\AccessRole;
use crm\src\components\Security\_common\mappers\AccessRoleMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * @extends ARepository<AccessRole>
 */
class AccessRoleRepository extends ARepository implements IAccessRoleRepository
{
    protected function getTableName(): string
    {
        return 'access_roles';
    }

    /**
     * @return class-string<AccessRole>
     */
    protected function getEntityClass(): string
    {
        return AccessRole::class;
    }

    protected function fromArray(): callable
    {
        return [AccessRoleMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var AccessRole $entity
         */
        return AccessRoleMapper::toArrayDb($entity);
    }

    public function getByName(string $name): ?AccessRole
    {
        $mapper = $this->fromArray();

        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('name = :name')
                ->limit(1)
                ->select(['name' => $name])
        )->first()->getObjectOrNullWithMapper($this->getEntityClass(), $mapper);
    }
}
