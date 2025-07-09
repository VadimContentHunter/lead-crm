<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\LeadManagement\_common\DTOs\StatusDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadStatusRepository;
use crm\src\components\StatusManagement\_entities\Status;
use crm\src\components\StatusManagement\_common\interfaces\IStatusRepository;

/**
 * Secure обёртка для StatusRepository с преобразованием в StatusDto.
 */
class SecureLeadStatusRepository implements ILeadStatusRepository
{
    private SecureWrapper $secure;

    public function __construct(
        IStatusRepository $statusRepository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($statusRepository, $accessGranter, $accessContext);
    }

    public function getByTitle(string $title): ?StatusDto
    {
        /**
 * @var Status|null $status
*/
        $status = $this->secure->__call('getByTitle', [$title]);
        return $status ? new StatusDto($status->id, $status->title) : null;
    }

    public function deleteByTitle(string $title): ?int
    {
        return $this->secure->__call('deleteByTitle', [$title]);
    }

    public function getColumnNames(): array
    {
        return $this->secure->__call('getColumnNames', []);
    }

    public function save(object|array $entity): ?int
    {
        return $this->secure->__call('save', [$entity]);
    }

    public function update(object|array $entityOrData): ?int
    {
        return $this->secure->__call('update', [$entityOrData]);
    }

    public function deleteById(int $id): ?int
    {
        return $this->secure->__call('deleteById', [$id]);
    }

    public function getById(int $id): ?StatusDto
    {
        /**
 * @var Status|null $status
*/
        $status = $this->secure->__call('getById', [$id]);
        return $status ? new StatusDto($status->id, $status->title) : null;
    }

    public function getAll(): array
    {
        /**
 * @var Status[] $statuses
*/
        $statuses = $this->secure->__call('getAll', []);
        return array_map(fn(Status $s) => new StatusDto($s->id, $s->title), $statuses);
    }

    public function getAllExcept(string $column = '', array $excludedValues = []): array
    {
        /**
 * @var Status[] $statuses
*/
        $statuses = $this->secure->__call('getAllExcept', [$column, $excludedValues]);
        return array_map(fn(Status $s) => new StatusDto($s->id, $s->title), $statuses);
    }

    public function getAllByColumnValues(string $column = '', array $values = []): array
    {
        /**
 * @var Status[] $statuses
*/
        $statuses = $this->secure->__call('getAllByColumnValues', [$column, $values]);
        return array_map(fn(Status $s) => new StatusDto($s->id, $s->title), $statuses);
    }
}
