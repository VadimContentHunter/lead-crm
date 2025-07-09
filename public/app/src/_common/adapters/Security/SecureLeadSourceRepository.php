<?php

namespace crm\src\_common\adapters\Security;

use crm\src\components\Security\SecureWrapper;
use crm\src\components\Security\_entities\AccessContext;
use crm\src\components\Security\_common\interfaces\IAccessGranter;
use crm\src\components\SourceManagement\_entities\Source;
use crm\src\components\LeadManagement\_common\DTOs\SourceDto;
use crm\src\components\LeadManagement\_common\interfaces\ILeadSourceRepository;
use crm\src\components\SourceManagement\_common\interfaces\ISourceRepository;

/**
 * Secure обёртка для SourceRepository с преобразованием в SourceDto.
 */
class SecureLeadSourceRepository implements ILeadSourceRepository
{
    private SecureWrapper $secure;

    public function __construct(
        ISourceRepository $repository,
        IAccessGranter $accessGranter,
        ?AccessContext $accessContext
    ) {
        $this->secure = new SecureWrapper($repository, $accessGranter, $accessContext);
    }

    public function getByTitle(string $title): ?SourceDto
    {
        /**
 * @var Source|null $source
*/
        $source = $this->secure->__call('getByTitle', [$title]);
        return $source ? new SourceDto($source->id, $source->title) : null;
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

    public function getById(int $id): ?SourceDto
    {
        /**
 * @var Source|null $source
*/
        $source = $this->secure->__call('getById', [$id]);
        return $source ? new SourceDto($source->id, $source->title) : null;
    }

    public function getAll(): array
    {
        /**
 * @var Source[] $sources
*/
        $sources = $this->secure->__call('getAll', []);
        return array_map(fn(Source $s) => new SourceDto($s->id, $s->title), $sources);
    }

    public function getAllExcept(string $column = '', array $excludedValues = []): array
    {
        /**
 * @var Source[] $sources
*/
        $sources = $this->secure->__call('getAllExcept', [$column, $excludedValues]);
        return array_map(fn(Source $s) => new SourceDto($s->id, $s->title), $sources);
    }

    public function getAllByColumnValues(string $column = '', array $values = []): array
    {
        /**
 * @var Source[] $sources
*/
        $sources = $this->secure->__call('getAllByColumnValues', [$column, $values]);
        return array_map(fn(Source $s) => new SourceDto($s->id, $s->title), $sources);
    }
}
