<?php

namespace crm\src\_common\repositories\LeadRepository;

use crm\src\_common\interfaces\ARepository;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadSourceRepository;
use crm\src\components\LeadManagement\_common\mappers\SourceDtoMapper;
use crm\src\services\Repositories\QueryBuilder\QueryBuilder;

/**
 * @extends ARepository<SourceDto>
 */
class LeadSourceRepository extends ARepository implements ILeadSourceRepository
{
    protected function getTableName(): string
    {
        return 'sources';
    }

    protected function getEntityClass(): string
    {
        return SourceDto::class;
    }

    protected function fromArray(): callable
    {
        return [SourceDtoMapper::class, 'fromArray'];
    }

    protected function toArray(object $entity): array
    {
        /**
 * @var SourceDto $entity
*/
        return SourceDtoMapper::toArray($entity);
    }

    public function getByTitle(string $title): ?SourceDto
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
