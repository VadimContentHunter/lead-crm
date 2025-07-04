<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\SourceManagement\_common\mappers\SourceMapper;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;

/**
 * @extends ARepository<Source>
 */
class SourceRepository extends ARepository implements ISourceRepository
{
    protected function getTableName(): string
    {
        return 'sources';
    }

    /**
     * @return class-string<Source>
     */
    protected function getEntityClass(): string
    {
        return Source::class;
    }

    protected function fromArray(): callable
    {
        return [SourceMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var Source $entity
         */
        return SourceMapper::toArray($entity);
    }

    public function deleteByTitle(string $title): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())
                ->where('title = :title')
                ->delete(['title' => $title])
        )->getInt();
    }

    public function getByTitle(string $title): ?Source
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())
                ->where('title = :title')
                ->select()
        )->getObjectOrNull($this->getEntityClass());
    }
}
