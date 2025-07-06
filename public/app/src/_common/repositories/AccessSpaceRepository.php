<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\Security\_common\interfaces\IAccessSpaceRepository;
use crm\src\components\Security\_entities\AccessSpace;
use crm\src\components\Security\_common\mappers\AccessSpaceMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * @extends ARepository<AccessSpace>
 */
class AccessSpaceRepository extends ARepository implements IAccessSpaceRepository
{
    protected function getTableName(): string
    {
        return 'access_spaces';
    }

    /**
     * @return class-string<AccessSpace>
     */
    protected function getEntityClass(): string
    {
        return AccessSpace::class;
    }

    protected function fromArray(): callable
    {
        return [AccessSpaceMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var AccessSpace $entity
         */
        return AccessSpaceMapper::toArrayDb($entity);
    }

    public function getByName(string $name): ?AccessSpace
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
