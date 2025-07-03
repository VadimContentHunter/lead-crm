<?php

namespace crm\src\_common\repositories\LeadRepository;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadStatusRepository;
use crm\src\components\LeadManagement\_common\mappers\StatusDtoMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * @extends ARepository<StatusDto>
 */
class LeadStatusRepository extends ARepository implements ILeadStatusRepository
{
    protected function getTableName(): string
    {
        return 'statuses';
    }

    protected function getEntityClass(): string
    {
        return StatusDto::class;
    }

    protected function fromArray(): callable
    {
        return [StatusDtoMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var StatusDto $entity
*/
        return StatusDtoMapper::toArray($entity);
    }

    public function getByTitle(string $title): ?StatusDto
    {
        $query = (new QueryBuilder())
            ->table($this->getTableName())
            ->where('title = :title')
            ->select(['title' => $title]);

        $result = $this->repository->executeQuery($query);

        return $result->isSuccess()
            ? $result->getObjectOrNull($this->getEntityClass())
            : null;
    }

    public function deleteByTitle(string $title): ?int
    {
        $query = (new QueryBuilder())
            ->table($this->getTableName())
            ->where('title = :title')
            ->delete(['title' => $title]);

        $result = $this->repository->executeQuery($query);

        return $result->isSuccess()
            ? $result->getInt()
            : null;
    }
}
