<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\Security\_common\interfaces\IAccessContextRepository;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\mappers\AccessContextMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * @extends ARepository<AccessContext>
 */
class AccessContextRepository extends ARepository implements IAccessContextRepository
{
    protected function getTableName(): string
    {
        return 'access_contexts';
    }

    /**
     * @return class-string<AccessContext>
     */
    protected function getEntityClass(): string
    {
        return AccessContext::class;
    }

    protected function fromArray(): callable
    {
        return [AccessContextMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var AccessContext $entity
         */
        return AccessContextMapper::toArrayDb($entity);
    }

    public function getBySessionHash(string $hash): ?AccessContext
    {
        $mapper = $this->fromArray();

        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('session_access_hash = :hash')
                ->limit(1)
                ->select(['hash' => $hash])
        )->first()->getObjectOrNullWithMapper($this->getEntityClass(), $mapper);
    }

    public function deleteBySessionHash(string $hash): bool
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())
                ->table($this->getTableName())
                ->where('session_access_hash = :hash')
                ->delete(['hash' => $hash])
        )->getBool() ?? false;
    }
}
