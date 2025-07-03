<?php

namespace crm\src\_common\repositories;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;
use crm\src\components\StatusManagement\_common\mappers\StatusMapper;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;

/**
 * @extends ARepository<Status>
 */
class StatusRepository extends ARepository implements IStatusRepository
{
    protected function getTableName(): string
    {
        return 'statuses';
    }

    protected function getEntityClass(): string
    {
        return Status::class;
    }

    protected function fromArray(): callable
    {
        return [StatusMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
         * @var Status $entity
         */
        return StatusMapper::toArray($entity);
    }

    public function deleteByTitle(string $title): ?int
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())
                ->where('title = :title')
                ->delete(['title' => $title])
        )->getInt();
    }

    public function getByTitle(string $title): ?Status
    {
        return $this->repository->executeQuery(
            (new QueryBuilder())->table($this->getTableName())
                ->where('title = :title')
                ->select()
        )->getObjectOrNull($this->getEntityClass());
    }
}
